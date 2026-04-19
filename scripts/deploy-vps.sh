#!/usr/bin/env bash
set -euo pipefail

APP_PATH="${APP_PATH:-/var/www/bizmark.id}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"

cd "$APP_PATH"

echo "[1/9] Put app into maintenance mode (best-effort)"
$PHP_BIN artisan down --retry=60 --render=errors::503 2>/dev/null || true

echo "[2/9] Fix Laravel runtime permissions"
sudo APP_PATH="$APP_PATH" WEB_USER="$WEB_USER" WEB_GROUP="$WEB_GROUP" bash "$APP_PATH/scripts/fix-laravel-permissions.sh"

echo "[3/9] Verify runtime permissions (fail-fast)"
$PHP_BIN artisan ops:permissions-check

echo "[4/9] Install PHP dependencies (production)"
$COMPOSER_BIN install --no-interaction --prefer-dist --no-dev --optimize-autoloader

echo "[5/9] Clear & rebuild caches"
$PHP_BIN artisan config:clear || true
$PHP_BIN artisan cache:clear || true
$PHP_BIN artisan route:clear || true
$PHP_BIN artisan view:clear || true

$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "[6/9] Run migrations"
$PHP_BIN artisan migrate --force

echo "[7/9] Restart queue"
$PHP_BIN artisan queue:restart || true

echo "[8/9] Reload PHP-FPM (best-effort)"
sudo systemctl reload php8.2-fpm 2>/dev/null || sudo systemctl reload php-fpm 2>/dev/null || true

echo "[9/9] Bring app back up"
$PHP_BIN artisan up 2>/dev/null || true

echo "Deploy finished."

