#!/usr/bin/env bash
#
# Rotate PostgreSQL password for the application user and update .env atomically.
#
# Usage:
#   ./scripts/rotate-db-password.sh [new-password]
#
# If no password is given, a random 32-char password is generated.
#
# Prerequisites:
#   - User harus bisa connect ke postgres sebagai superuser (mis. lewat peer auth
#     dengan `sudo -u postgres psql`), atau sesuaikan PSQL_SUPERUSER di bawah.
#   - Jalankan dari root project (yang berisi .env).
#   - Setelah sukses, restart service: supervisorctl restart bizmark-worker:* &&
#     sudo systemctl reload php8.2-fpm (atau sesuai versi PHP-mu).
#
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

if [[ ! -f .env ]]; then
  echo "ERROR: .env tidak ditemukan di $PROJECT_ROOT" >&2
  exit 1
fi

DB_USERNAME="$(grep -E '^DB_USERNAME=' .env | head -1 | cut -d= -f2- | tr -d '"' )"
DB_DATABASE="$(grep -E '^DB_DATABASE=' .env | head -1 | cut -d= -f2- | tr -d '"' )"
DB_HOST="$(grep -E '^DB_HOST=' .env | head -1 | cut -d= -f2- | tr -d '"' )"
DB_PORT="$(grep -E '^DB_PORT=' .env | head -1 | cut -d= -f2- | tr -d '"' )"

if [[ -z "${DB_USERNAME}" ]]; then
  echo "ERROR: DB_USERNAME kosong di .env" >&2
  exit 1
fi

NEW_PASSWORD="${1:-}"
if [[ -z "$NEW_PASSWORD" ]]; then
  NEW_PASSWORD="$(openssl rand -base64 24 | tr -d '+/=' | cut -c1-32)"
fi

echo "==========================================="
echo "  Rotate password DB"
echo "  User     : $DB_USERNAME"
echo "  Database : $DB_DATABASE"
echo "  Host     : $DB_HOST:$DB_PORT"
echo "==========================================="
read -p "Lanjut? [y/N] " ans
[[ "$ans" =~ ^[Yy]$ ]] || { echo "Dibatalkan."; exit 0; }

# Backup .env sebelum modifikasi
BACKUP=".env.pre-rotate.$(date +%Y%m%d-%H%M%S)"
cp .env "$BACKUP"
echo "Backup .env -> $BACKUP"

# Update password di postgres.
# NOTE: escape single quotes dengan dobel quote postgres.
ESCAPED="${NEW_PASSWORD//\'/\'\'}"
echo "Menjalankan ALTER USER di postgres..."
if command -v sudo >/dev/null 2>&1; then
  sudo -u postgres psql -v ON_ERROR_STOP=1 -c "ALTER USER \"$DB_USERNAME\" WITH PASSWORD '$ESCAPED';"
else
  psql -h "$DB_HOST" -p "$DB_PORT" -U postgres -d postgres -v ON_ERROR_STOP=1 \
    -c "ALTER USER \"$DB_USERNAME\" WITH PASSWORD '$ESCAPED';"
fi

# Update .env (escape & karena sed)
ESCAPED_FOR_SED="${NEW_PASSWORD//&/\\&}"
ESCAPED_FOR_SED="${ESCAPED_FOR_SED//\//\\/}"
if grep -qE '^DB_PASSWORD=' .env; then
  sed -i.tmp -E "s|^DB_PASSWORD=.*|DB_PASSWORD=\"${ESCAPED_FOR_SED}\"|" .env
else
  echo "DB_PASSWORD=\"${NEW_PASSWORD}\"" >> .env
fi
rm -f .env.tmp

# Verifikasi koneksi dengan password baru
echo "Testing koneksi dengan password baru..."
PGPASSWORD="$NEW_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -c "SELECT 1;" > /dev/null

echo ""
echo "=== SUKSES ==="
echo "DB password untuk '$DB_USERNAME' telah dirotasi."
echo ".env ter-update (backup di $BACKUP)."
echo ""
echo "LANGKAH SELANJUTNYA WAJIB:"
echo "  1. php artisan config:clear"
echo "  2. php artisan config:cache  (untuk production)"
echo "  3. supervisorctl restart bizmark-worker:*"
echo "  4. sudo systemctl reload php-fpm  (sesuaikan nama service)"
echo "  5. Reload nginx: sudo systemctl reload nginx"
echo ""
echo "Password baru (catat sekarang!):"
echo "  $NEW_PASSWORD"
