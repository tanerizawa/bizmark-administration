#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

APP_PATH="${APP_PATH:-$ROOT_DIR}"
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"

STORAGE_DIR="$APP_PATH/storage"
BOOTSTRAP_CACHE_DIR="$APP_PATH/bootstrap/cache"

if [[ ! -d "$STORAGE_DIR" ]]; then
  echo "storage directory not found: $STORAGE_DIR" >&2
  exit 1
fi

if [[ ! -d "$BOOTSTRAP_CACHE_DIR" ]]; then
  echo "bootstrap/cache directory not found: $BOOTSTRAP_CACHE_DIR" >&2
  exit 1
fi

echo "Target app path: $APP_PATH"
echo "Web user/group: $WEB_USER:$WEB_GROUP"

echo "Setting ownership (group) for runtime dirs..."
chgrp -R "$WEB_GROUP" "$STORAGE_DIR" "$BOOTSTRAP_CACHE_DIR"

echo "Setting directory permissions (g+s, 775) and file permissions (664)..."
find "$STORAGE_DIR" "$BOOTSTRAP_CACHE_DIR" -type d -exec chmod 2775 {} +
find "$STORAGE_DIR" "$BOOTSTRAP_CACHE_DIR" -type f -exec chmod 0664 {} +

if command -v setfacl >/dev/null 2>&1; then
  echo "Applying ACL for web user/group (recommended)..."
  setfacl -R -m "u:${WEB_USER}:rwx" -m "g:${WEB_GROUP}:rwx" "$STORAGE_DIR" "$BOOTSTRAP_CACHE_DIR" || true
  setfacl -R -d -m "u:${WEB_USER}:rwx" -m "g:${WEB_GROUP}:rwx" "$STORAGE_DIR" "$BOOTSTRAP_CACHE_DIR" || true
else
  echo "setfacl not found; skipping ACL."
fi

echo "Verifying write access as web user (best-effort)..."
for d in \
  "$STORAGE_DIR/framework/views" \
  "$STORAGE_DIR/framework/cache" \
  "$STORAGE_DIR/framework/sessions" \
  "$STORAGE_DIR/logs" \
  "$BOOTSTRAP_CACHE_DIR"
do
  mkdir -p "$d"
  test_file="$d/.permcheck-$$"
  if command -v sudo >/dev/null 2>&1; then
    sudo -u "$WEB_USER" bash -c "echo permcheck > '$test_file'" 2>/dev/null || true
  else
    echo permcheck > "$test_file" 2>/dev/null || true
  fi
  if [[ -f "$test_file" ]]; then
    rm -f "$test_file"
    echo "OK: $d"
  else
    echo "FAIL: $d (not writable for $WEB_USER)" >&2
  fi
done

echo "Done. If you use PHP-FPM, reload it after fixes (e.g., systemctl reload php8.2-fpm)."

