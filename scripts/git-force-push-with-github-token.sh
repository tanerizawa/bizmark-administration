#!/usr/bin/env bash
# Force-push semua branch + tag ke origin memakai HTTPS + PAT (tanpa menyimpan token di remote URL).
# Set: export GITHUB_TOKEN=ghp_...  (repo harus punya scope `repo` / write packages tidak diperlukan)
set -euo pipefail

TOKEN="${GITHUB_TOKEN:-${GH_TOKEN:-}}"
if [[ -z "$TOKEN" ]]; then
  echo "ERROR: set GITHUB_TOKEN atau GH_TOKEN (PAT GitHub)." >&2
  exit 1
fi

ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"

ORIGIN_URL="$(git remote get-url origin)"
# Harapkan https://github.com/OWNER/REPO.git
if [[ "$ORIGIN_URL" != https://github.com/* ]]; then
  echo "ERROR: origin bukan URL https://github.com/... — sesuaikan skrip atau remote URL." >&2
  exit 1
fi

PATH_PART="${ORIGIN_URL#https://github.com/}"
AUTH_URL="https://x-access-token:${TOKEN}@github.com/${PATH_PART}"

echo "Force-push origin (semua refs)…"
git push --force --all "$AUTH_URL"
git push --force --tags "$AUTH_URL"
echo "Selesai. (Token tidak disimpan di git config.)"
