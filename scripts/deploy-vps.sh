#!/usr/bin/env bash
set -euo pipefail

APP_PATH="${APP_PATH:-/var/www/bizmark.id}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"

cd "$APP_PATH"

echo "[1/10] Maintenance mode ON"
$PHP_BIN artisan down --retry=60 --render=errors::503 2>/dev/null || true

echo "[2/10] Fix runtime permissions"
sudo APP_PATH="$APP_PATH" WEB_USER="$WEB_USER" WEB_GROUP="$WEB_GROUP" \
    bash "$APP_PATH/scripts/fix-laravel-permissions.sh"

echo "[3/10] Verify permissions"
$PHP_BIN artisan ops:permissions-check 2>/dev/null || true

echo "[4/10] Install PHP dependencies"
$COMPOSER_BIN install --no-interaction --prefer-dist --no-dev --optimize-autoloader

echo "[5/10] Build frontend assets (if package.json changed)"
if [ -f package.json ]; then
    npm ci --prefer-offline 2>/dev/null || npm install --prefer-offline
    npm run build
fi

echo "[6/10] Clear & rebuild caches"
$PHP_BIN artisan config:clear
$PHP_BIN artisan cache:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
$PHP_BIN artisan event:clear 2>/dev/null || true

$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache 2>/dev/null || true

echo "[7/10] Run migrations"
$PHP_BIN artisan migrate --force

echo "[8/10] Restart queue workers"
$PHP_BIN artisan queue:restart

echo "[9/10] Reload PHP-FPM"
sudo systemctl reload php8.4-fpm 2>/dev/null \
    || sudo systemctl reload php8.2-fpm 2>/dev/null \
    || sudo systemctl reload php-fpm 2>/dev/null \
    || true

echo "[10/10] Maintenance mode OFF"
$PHP_BIN artisan up

echo "Deploy finished at $(date '+%Y-%m-%d %H:%M:%S')"
