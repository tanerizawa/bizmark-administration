#!/usr/bin/env bash
# Verifikasi lokal sesuai docs/SECURITY_GIT_HISTORY_SCRUB.md §4 (tanpa push).
set -euo pipefail
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
echo "== config:clear (hindari config ter-cache memakai .env produksi saat phpunit) =="
php artisan config:clear -q
echo ""
echo "== routes/admin partials (<?php) =="
bash scripts/verify-admin-route-partials.sh
echo ""
echo "== git-audit-sensitive-paths =="
bash scripts/git-audit-sensitive-paths.sh
echo ""
echo "== phpunit tests/Unit =="
vendor/bin/phpunit tests/Unit --colors=never
echo ""
echo "== route:list (count tail) =="
php artisan route:list --except-vendor 2>&1 | tail -3
echo ""
echo "OK. Langkah berikutnya: git push origin --force --all && git push origin --force --tags"
