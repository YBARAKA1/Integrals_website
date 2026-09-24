#!/usr/bin/env python3
"""Host-side proxy so Docker WordPress can reach live SHA / DHA facility APIs.

Also maintains an FR-code cache: the SHA guest API only accepts name /
registration_number, so FID-… lookups are resolved via DHA FHIR and this cache
(populated from successful SHA portal hits).
"""
from __future__ import annotations

import json
import os
import re
import threading
import time
import urllib.error
import urllib.parse
import urllib.request
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer

FHIR_UPSTREAM = "https://ilm-hie.dha.go.ke/fhir"
SHA_FACILITIES = "https://api.provider.sha.go.ke/v1/facilities/"
LISTEN = ("0.0.0.0", 18787)
ROOT = os.path.dirname(os.path.abspath(__file__))
CACHE_PATH = os.path.join(ROOT, ".facility-fr-cache.json")
HIE_ENV_PATH = os.path.join(ROOT, ".sha-hie.env")
_cache_lock = threading.Lock()
_token_cache: dict = {}


def _load_hie_creds() -> dict:
    """Same fields as Institution → SHA Setup (url, consumer key/secret)."""
    creds = {
        "url": os.environ.get("INTEGRAL_SHA_HIE_URL")
        or os.environ.get("SHA_HIE_URL")
        or "https://ilm-hie.dha.go.ke/middleware",
        "client_id": os.environ.get("INTEGRAL_SHA_CLIENT_ID")
        or os.environ.get("SHA_CLIENT_ID")
        or "",
        "client_secret": os.environ.get("INTEGRAL_SHA_CLIENT_SECRET")
        or os.environ.get("SHA_CLIENT_SECRET")
        or "",
    }
    if os.path.isfile(HIE_ENV_PATH):
        try:
            with open(HIE_ENV_PATH, "r", encoding="utf-8") as fh:
                for line in fh:
                    line = line.strip()
                    if not line or line.startswith("#") or "=" not in line:
                        continue
                    k, v = line.split("=", 1)
                    k, v = k.strip(), v.strip().strip("\"'")
                    if k in ("INTEGRAL_SHA_HIE_URL", "SHA_HIE_URL", "url"):
                        creds["url"] = v
                    elif k in (
                        "INTEGRAL_SHA_CLIENT_ID",
                        "SHA_CLIENT_ID",
                        "client_id",
                        "consumer_key",
                    ):
                        creds["client_id"] = v
                    elif k in (
                        "INTEGRAL_SHA_CLIENT_SECRET",
                        "SHA_CLIENT_SECRET",
                        "client_secret",
                        "consumer_secret",
                    ):
                        creds["client_secret"] = v
        except Exception:
            pass
    return creds


def _hie_token(base: str, client_id: str, client_secret: str) -> str | None:
    key = f"{base}|{client_id}"
    hit = _token_cache.get(key)
    if hit and hit.get("exp", 0) > time.time():
        return hit["token"]
    body = urllib.parse.urlencode(
        {"client_id": client_id, "client_secret": client_secret}
    ).encode()
    status, raw, _ = _forward(
        "POST",
        base.rstrip("/") + "/api/v1/tenants/token",
        body,
        "application/x-www-form-urlencoded",
    )
    if status < 200 or status >= 300 or not raw:
        return None
    try:
        data = json.loads(raw.decode("utf-8", errors="ignore"))
    except Exception:
        return None
    token = data.get("access_token") or ""
    if not token:
        return None
    expires = int(data.get("expires_in") or 300)
    _token_cache[key] = {"token": token, "exp": time.time() + max(60, expires - 30)}
    return token


def _hie_facility_search(identifier: str, identifier_type: str):
    """Mirrors Legacy-integral ShaSetupServicesImpl.searchOrganization()."""
    creds = _load_hie_creds()
    if not creds["client_id"] or not creds["client_secret"]:
        return 501, b'{"error":"hie_credentials_missing"}', "application/json"
    base = creds["url"].rstrip("/")
    token = _hie_token(base, creds["client_id"], creds["client_secret"])
    if not token:
        return 502, b'{"error":"hie_token_failed"}', "application/json"
    url = (
        base
        + "/api/v1/facilities/search?identifier="
        + urllib.parse.quote(identifier, safe="")
        + "&identifier-type="
        + urllib.parse.quote(identifier_type, safe="")
    )
    headers = {
        "Accept": "application/json",
        "Authorization": "Bearer " + token,
        "User-Agent": "IntegralWP-FacilityProxy/1.2",
    }
    req = urllib.request.Request(url, headers=headers, method="GET")
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            return resp.getcode(), resp.read(), resp.headers.get("Content-Type", "application/json")
    except urllib.error.HTTPError as e:
        ctype = e.headers.get("Content-Type", "application/json") if e.headers else "application/json"
        return e.code, e.read() or b"", ctype
    except Exception as e:
        payload = json.dumps({"error": "hie_search_failed", "message": str(e)}).encode()
        return 502, payload, "application/json"


def _forward(method: str, url: str, body: bytes | None = None, content_type: str | None = None):
    headers = {
        "Accept": "application/json, application/fhir+json",
        "User-Agent": "IntegralWP-FacilityProxy/1.2",
    }
    if content_type:
        headers["Content-Type"] = content_type
    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            return resp.getcode(), resp.read(), resp.headers.get("Content-Type", "application/json")
    except urllib.error.HTTPError as e:
        ctype = e.headers.get("Content-Type", "application/json") if e.headers else "application/json"
        return e.code, e.read() or b"", ctype
    except Exception as e:
        payload = json.dumps({"error": "proxy_upstream_failed", "message": str(e)}).encode()
        return 502, payload, "application/json"


def _load_cache() -> dict:
    if not os.path.isfile(CACHE_PATH):
        return {}
    try:
        with open(CACHE_PATH, "r", encoding="utf-8") as fh:
            data = json.load(fh)
        return data if isinstance(data, dict) else {}
    except Exception:
        return {}


def _save_cache(cache: dict) -> None:
    try:
        tmp = CACHE_PATH + ".tmp"
        with open(tmp, "w", encoding="utf-8") as fh:
            json.dump(cache, fh, indent=2)
        os.replace(tmp, CACHE_PATH)
    except Exception:
        pass


def _index_sha_payload(raw: bytes) -> None:
    try:
        data = json.loads(raw.decode("utf-8", errors="ignore"))
    except Exception:
        return
    rows = []
    if isinstance(data, dict) and isinstance(data.get("results"), list):
        rows = data["results"]
    elif isinstance(data, dict) and data.get("national_identifier"):
        rows = [data]
    if not rows:
        return
    with _cache_lock:
        cache = _load_cache()
        changed = False
        for row in rows:
            if not isinstance(row, dict):
                continue
            ids = {}
            for ident in row.get("identifiers") or []:
                if isinstance(ident, dict) and ident.get("identifier_type"):
                    ids[ident["identifier_type"]] = ident.get("identifier") or ""
            fr = ids.get("fr-code") or row.get("national_identifier") or ""
            if not fr or not row.get("name"):
                continue
            key = str(fr).upper()
            cache[key] = {
                "frCode": fr,
                "fidCode": ids.get("fid") or "",
                "name": row.get("name") or row.get("official_name") or "",
                "facilityType": row.get("facility_type") or row.get("bp_type") or "",
                "level": row.get("bp_level") or row.get("keph_level") or "",
                "ownership": row.get("bp_ownership") or "",
                "county": row.get("county") or "",
                "subCounty": row.get("sub_county") or "",
                "town": row.get("town") or "",
                "phone": row.get("phone") or row.get("telephone") or "",
                "email": row.get("email") or "",
                "licenseStatus": ("Reg #" + ids["registration-number"]) if ids.get("registration-number") else "",
                "shaStatus": row.get("status") or "",
                "sladeCode": row.get("slade_code") or "",
                "uuid": row.get("id") or "",
                "source": "sha-portal",
            }
            changed = True
        if changed:
            _save_cache(cache)


def _fhir_by_identifier(code: str):
    url = (
        FHIR_UPSTREAM.rstrip("/")
        + "/Organization?identifier="
        + urllib.parse.quote(code, safe="")
        + "&_count=5"
    )
    status, body, _ = _forward("GET", url)
    if status < 200 or status >= 300 or not body:
        return None
    try:
        bundle = json.loads(body.decode("utf-8", errors="ignore"))
    except Exception:
        return None
    entries = bundle.get("entry") or []
    if not entries:
        return None
    org = (entries[0] or {}).get("resource") or {}
    name = org.get("name") or ""
    if not name:
        return None
    fr_code = fid = reg = ""
    for ident in org.get("identifier") or []:
        coding = ((ident.get("type") or {}).get("coding") or [{}])[0]
        itype = (coding.get("code") or "").lower()
        value = ident.get("value") or ""
        if itype == "fr-code":
            fr_code = value
        elif itype == "fid":
            fid = value
        elif itype == "registration-number":
            reg = value
        elif not fr_code and re.match(r"^FID[-_]", value or "", re.I):
            fr_code = value
    return {
        "frCode": fr_code or code,
        "fidCode": fid,
        "name": name,
        "facilityType": ((org.get("type") or [{}])[0].get("text") if org.get("type") else "") or "",
        "level": "",
        "ownership": "",
        "county": "",
        "subCounty": "",
        "town": "",
        "phone": "",
        "email": "",
        "licenseStatus": ("Reg #" + reg) if reg else "",
        "shaStatus": "",
        "sladeCode": "",
        "uuid": "",
        "source": "dha-fhir",
    }


def _lookup_fr(code: str):
    key = code.strip().upper()
    with _cache_lock:
        cache = _load_cache()
        hit = cache.get(key)
        if hit:
            out = dict(hit)
            out["source"] = out.get("source") or "fr-cache"
            return out

    fhir = _fhir_by_identifier(code.strip())
    if fhir:
        with _cache_lock:
            cache = _load_cache()
            cache[key] = fhir
            _save_cache(cache)
        return fhir

    # FID-47-108679-9 → try middle facility id on FHIR
    m = re.match(r"^FID[-_](\d+)[-_](\d+)[-_](\d+)$", key, re.I)
    if m:
        mid = m.group(2)
        fhir = _fhir_by_identifier(mid)
        if fhir:
            with _cache_lock:
                cache = _load_cache()
                cache[key] = fhir
                _save_cache(cache)
            return fhir

    # Refresh from SHA detail if we somehow have a UUID under another key — skip
    return None


class Handler(BaseHTTPRequestHandler):
    def log_message(self, fmt, *args):
        pass

    def _reply(self, status: int, body: bytes, ctype: str):
        self.send_response(status)
        self.send_header("Content-Type", ctype)
        self.send_header("Content-Length", str(len(body)))
        self.send_header("Access-Control-Allow-Origin", "*")
        self.end_headers()
        self.wfile.write(body)

    def do_OPTIONS(self):
        self.send_response(204)
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET, POST, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type, Accept")
        self.end_headers()

    def do_GET(self):
        parsed = urllib.parse.urlparse(self.path)
        path = parsed.path

        if path.startswith("/fhir"):
            target = FHIR_UPSTREAM + self.path[len("/fhir") :]
            status, body, ctype = _forward("GET", target)
            self._reply(status, body, ctype)
            return

        # GET /hie/facilities/search?identifier=&identifier-type=
        # Same as Integral HMIS institution setup searchOrganization.
        if path.startswith("/hie/facilities/search"):
            qs = urllib.parse.parse_qs(parsed.query)
            identifier = (qs.get("identifier") or [""])[0].strip()
            identifier_type = (qs.get("identifier-type") or qs.get("identifier_type") or ["fr-code"])[0].strip() or "fr-code"
            if not identifier:
                self._reply(400, b'{"error":"missing_identifier"}', "application/json")
                return
            status, body, ctype = _hie_facility_search(identifier, identifier_type)
            self._reply(status, body, ctype)
            return

        # GET /fr/<FID-…>  or  /fr?code=
        if path == "/fr" or path.startswith("/fr/"):
            code = ""
            if path.startswith("/fr/") and len(path) > 4:
                code = urllib.parse.unquote(path[4:])
            if not code:
                code = urllib.parse.parse_qs(parsed.query).get("code", [""])[0]
            code = (code or "").strip()
            if not code:
                self._reply(400, b'{"error":"missing_code"}', "application/json")
                return
            hit = _lookup_fr(code)
            if not hit:
                self._reply(404, b'{"error":"not_found"}', "application/json")
                return
            self._reply(200, json.dumps(hit).encode(), "application/json")
            return

        self._reply(404, b'{"error":"not_found"}', "application/json")

    def do_POST(self):
        # Same guest search as https://portal.sha.go.ke/facility-lookup
        if self.path.startswith("/sha/facilities"):
            length = int(self.headers.get("Content-Length", "0") or 0)
            raw = self.rfile.read(length) if length else b"{}"
            qs = ""
            if "?" in self.path:
                qs = self.path[self.path.index("?") :]
            target = SHA_FACILITIES.rstrip("/") + "/" + qs
            status, body, ctype = _forward("POST", target, raw, "application/json")
            if 200 <= status < 300 and body:
                _index_sha_payload(body)
            self._reply(status, body, ctype)
            return
        self._reply(404, b'{"error":"not_found"}', "application/json")


if __name__ == "__main__":
    httpd = ThreadingHTTPServer(LISTEN, Handler)
    print(
        f"Facility proxy on http://{LISTEN[0]}:{LISTEN[1]} "
        f"(FHIR + SHA portal + HIE search + FR cache)",
        flush=True,
    )
    httpd.serve_forever()
