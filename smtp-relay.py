#!/usr/bin/env python3
"""
Plain SMTP listener for the WordPress container → forwards to mail.integral.co.ke.

The WP PHP 7.4 image cannot complete TLS to mail.integral.co.ke:465, while the
host can. This relay accepts unauthenticated SMTP and submits via SMTP_SSL using
credentials from .mail.env.
"""
from __future__ import annotations

import argparse
import email
import email.utils
import smtplib
import socket
import ssl
import sys
import threading
from pathlib import Path


ROOT = Path(__file__).resolve().parent


def load_mail_env(path: Path) -> dict:
    cfg = {}
    if not path.is_file():
        return cfg
    for line in path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        cfg[k.strip()] = v.strip().strip("\"'")
    return cfg


def forward(cfg: dict, mailfrom: str, rcpttos: list, payload: bytes) -> None:
    host = cfg.get("INTEGRAL_SMTP_UPSTREAM_HOST") or "mail.integral.co.ke"
    port = int(cfg.get("INTEGRAL_SMTP_UPSTREAM_PORT") or "465")
    user = cfg.get("INTEGRAL_SMTP_UPSTREAM_USER") or cfg.get("INTEGRAL_SMTP_USER", "")
    password = cfg.get("INTEGRAL_SMTP_UPSTREAM_PASS") or cfg.get("INTEGRAL_SMTP_PASS", "")
    secure = (
        cfg.get("INTEGRAL_SMTP_UPSTREAM_SECURE")
        or cfg.get("INTEGRAL_SMTP_SECURE")
        or "ssl"
    ).lower()

    if host in ("host.docker.internal", "integral-mailhog", "127.0.0.1", "localhost", ""):
        host, port, secure = "mail.integral.co.ke", 465, "ssl"

    ctx = ssl.create_default_context()
    if secure == "ssl" or port == 465:
        client = smtplib.SMTP_SSL(host, port, timeout=45, context=ctx)
    else:
        client = smtplib.SMTP(host, port, timeout=45)
        client.ehlo()
        client.starttls(context=ctx)
        client.ehlo()

    with client:
        if user:
            client.login(user, password)
        rcpts = list(rcpttos)
        if not rcpts:
            msg = email.message_from_bytes(payload)
            for hdr in ("To", "Cc", "Bcc"):
                for _, addr in email.utils.getaddresses(msg.get_all(hdr, [])):
                    if addr:
                        rcpts.append(addr)
        if not rcpts:
            raise RuntimeError("no recipients")
        sender = mailfrom or cfg.get("INTEGRAL_SMTP_FROM") or user
        client.sendmail(sender, rcpts, payload)
        print(f"[smtp-relay] forwarded to {', '.join(rcpts)} via {host}:{port}", flush=True)


def handle_client(conn: socket.socket, addr, cfg: dict) -> None:
    peer = f"{addr[0]}:{addr[1]}"
    try:
        conn.sendall(b"220 integral-smtp-relay ready\r\n")
        mailfrom = ""
        rcpttos: list[str] = []
        buf = b""
        while True:
            chunk = conn.recv(4096)
            if not chunk:
                break
            buf += chunk
            while b"\r\n" in buf:
                line, buf = buf.split(b"\r\n", 1)
                try:
                    text = line.decode("utf-8", errors="replace").strip()
                except Exception:
                    text = ""
                upper = text.upper()

                if upper.startswith("EHLO") or upper.startswith("HELO"):
                    conn.sendall(
                        b"250-integral-relay\r\n"
                        b"250-SIZE 35840000\r\n"
                        b"250-8BITMIME\r\n"
                        b"250 OK\r\n"
                    )
                elif upper.startswith("MAIL FROM:"):
                    mailfrom = text.split(":", 1)[1].strip().strip("<>")
                    rcpttos = []
                    conn.sendall(b"250 OK\r\n")
                elif upper.startswith("RCPT TO:"):
                    rcpt = text.split(":", 1)[1].strip().strip("<>")
                    if rcpt:
                        rcpttos.append(rcpt)
                    conn.sendall(b"250 OK\r\n")
                elif upper == "DATA":
                    conn.sendall(b"354 End data with <CR><LF>.<CR><LF>\r\n")
                    data = buf
                    buf = b""
                    while True:
                        if b"\r\n.\r\n" in data:
                            payload, rest = data.split(b"\r\n.\r\n", 1)
                            buf = rest
                            break
                        more = conn.recv(8192)
                        if not more:
                            break
                        data += more
                    # Undo dot-stuffing
                    payload = payload.replace(b"\r\n..", b"\r\n.")
                    try:
                        forward(cfg, mailfrom, rcpttos, payload)
                        conn.sendall(b"250 OK queued\r\n")
                    except Exception as exc:
                        print(f"[smtp-relay] FAIL peer={peer}: {exc}", flush=True)
                        conn.sendall(f"451 {exc}\r\n".encode("utf-8", errors="replace"))
                    mailfrom = ""
                    rcpttos = []
                elif upper == "RSET":
                    mailfrom = ""
                    rcpttos = []
                    conn.sendall(b"250 OK\r\n")
                elif upper == "NOOP":
                    conn.sendall(b"250 OK\r\n")
                elif upper == "QUIT":
                    conn.sendall(b"221 Bye\r\n")
                    return
                elif upper.startswith("STARTTLS") or upper.startswith("AUTH"):
                    conn.sendall(b"502 Command not implemented\r\n")
                else:
                    conn.sendall(b"502 Command not implemented\r\n")
    except Exception as exc:
        print(f"[smtp-relay] connection error {peer}: {exc}", flush=True)
    finally:
        try:
            conn.close()
        except Exception:
            pass


def serve(host: str, port: int, cfg: dict) -> None:
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    sock.bind((host, port))
    sock.listen(32)
    print(f"[smtp-relay] listen {host}:{port}", flush=True)
    while True:
        conn, addr = sock.accept()
        threading.Thread(target=handle_client, args=(conn, addr, cfg), daemon=True).start()


def main() -> int:
    parser = argparse.ArgumentParser(description="Integral SMTP relay")
    parser.add_argument("--host", default="0.0.0.0")
    parser.add_argument("--port", type=int, default=2525)
    parser.add_argument("--env", type=Path, default=ROOT / ".mail.env")
    args = parser.parse_args()

    cfg = load_mail_env(args.env)
    user = cfg.get("INTEGRAL_SMTP_UPSTREAM_USER") or cfg.get("INTEGRAL_SMTP_USER", "")
    password = cfg.get("INTEGRAL_SMTP_UPSTREAM_PASS") or cfg.get("INTEGRAL_SMTP_PASS", "")
    if not user or not password:
        print("[smtp-relay] missing upstream USER/PASS in .mail.env", file=sys.stderr)
        return 1

    up_host = cfg.get("INTEGRAL_SMTP_UPSTREAM_HOST") or "mail.integral.co.ke"
    up_port = cfg.get("INTEGRAL_SMTP_UPSTREAM_PORT") or "465"
    print(f"[smtp-relay] upstream {up_host}:{up_port} as {user}", flush=True)

    try:
        serve(args.host, args.port, cfg)
    except KeyboardInterrupt:
        print("[smtp-relay] stopped", flush=True)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
