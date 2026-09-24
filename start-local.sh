#!/usr/bin/env bash
# WordPress 4.9 needs PHP 7.4 — do NOT use host `php -S` (PHP 8.4).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
# Ensure facility registry proxy is running (SHA portal + DHA FHIR)
PROXY_PORT=18787
PROXY_PID_FILE="$ROOT/.facility-fhir-proxy.pid"
MAILHOG_NAME="integral-mailhog"
SMTP_RELAY_PID_FILE="$ROOT/.smtp-relay.pid"
SMTP_RELAY_PORT=2525

start_proxy() {
  if [[ -f "$PROXY_PID_FILE" ]] && kill -0 "$(cat "$PROXY_PID_FILE")" 2>/dev/null; then
    return 0
  fi
  # Load Institution SHA Setup credentials for HIE facility search (optional).
  if [[ -f "$ROOT/.sha-hie.env" ]]; then
    set -a
    # shellcheck disable=SC1091
    source "$ROOT/.sha-hie.env"
    set +a
  fi
  nohup python3 "$ROOT/facility-fhir-proxy.py" >"$ROOT/.facility-fhir-proxy.log" 2>&1 &
  echo $! >"$PROXY_PID_FILE"
  sleep 0.5
}

start_smtp_relay() {
  # Production credentials present → run host relay (Docker cannot TLS to mail host).
  if [[ -f "$ROOT/.mail.env" ]] && grep -q '^INTEGRAL_SMTP_UPSTREAM_HOST=mail.integral.co.ke' "$ROOT/.mail.env" 2>/dev/null; then
    if [[ -f "$SMTP_RELAY_PID_FILE" ]] && kill -0 "$(cat "$SMTP_RELAY_PID_FILE")" 2>/dev/null; then
      return 0
    fi
    nohup python3 "$ROOT/smtp-relay.py" --port "$SMTP_RELAY_PORT" \
      >"$ROOT/.smtp-relay.log" 2>&1 &
    echo $! >"$SMTP_RELAY_PID_FILE"
    sleep 0.4
    return 0
  fi
}

start_mailhog() {
  # Skip Mailhog when production relay is configured.
  if [[ -f "$ROOT/.mail.env" ]] && grep -q '^INTEGRAL_SMTP_UPSTREAM_HOST=mail.integral.co.ke' "$ROOT/.mail.env" 2>/dev/null; then
    return 0
  fi
  if docker ps --format '{{.Names}}' | grep -qx "$MAILHOG_NAME"; then
    return 0
  fi
  docker rm -f "$MAILHOG_NAME" >/dev/null 2>&1 || true
  docker run -d --name "$MAILHOG_NAME" \
    --network integral-wp \
    -p 1025:1025 \
    -p 8025:8025 \
    mailhog/mailhog:latest >/dev/null 2>&1 || true
  # Point WordPress at Mailhog when no production .mail.env exists yet.
  if [[ ! -f "$ROOT/.mail.env" ]]; then
    cat >"$ROOT/.mail.env" <<'EOF'
INTEGRAL_MAIL_TO=support@integral.co.ke
INTEGRAL_SMTP_FROM=no-reply@integral.co.ke
INTEGRAL_SMTP_FROM_NAME=Integral Software
INTEGRAL_SMTP_HOST=host.docker.internal
INTEGRAL_SMTP_PORT=1025
INTEGRAL_SMTP_SECURE=
INTEGRAL_SMTP_USER=
INTEGRAL_SMTP_PASS=
EOF
  fi
}

docker start integral-mysql >/dev/null

start_proxy
start_smtp_relay
start_mailhog

if ! docker ps --format '{{.Names}}' | grep -qx integral-wordpress; then
  docker rm -f integral-wordpress >/dev/null 2>&1 || true
  docker run -d --name integral-wordpress \
    --network integral-wp \
    --add-host=host.docker.internal:host-gateway \
    -p 8080:80 \
    -e WORDPRESS_DB_HOST=integral-mysql:3306 \
    -e INTEGRAL_FHIR_API_URL=http://host.docker.internal:${PROXY_PORT}/fhir \
    -v "$ROOT:/var/www/html" \
    integral-wp74 >/dev/null
else
  # Ensure proxy URL is available even if container was already running
  docker start integral-wordpress >/dev/null 2>&1 || true
fi

echo "Site: http://localhost:8080"
echo "Login: http://localhost:8080/wp-login.php"
echo "Facility FHIR proxy: http://127.0.0.1:${PROXY_PORT}/fhir"
if [[ -f "$ROOT/.mail.env" ]] && grep -q '^INTEGRAL_SMTP_UPSTREAM_HOST=mail.integral.co.ke' "$ROOT/.mail.env" 2>/dev/null; then
  echo "Mail: relay :${SMTP_RELAY_PORT} → mail.integral.co.ke (see .smtp-relay.log)"
else
  echo "Mail UI (Mailhog): http://127.0.0.1:8025"
fi
echo "(Stop with: docker stop integral-wordpress)"
