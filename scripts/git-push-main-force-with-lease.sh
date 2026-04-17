#!/usr/bin/env bash
# Push main ke origin setelah histori divergen (mis. pasca git filter-repo).
# --force-with-lease gagal jika origin/main bergerak sejak fetch terakhir (lebih aman dari --force).
# Jika origin memakai HTTPS tanpa credential helper, set GITHUB_TOKEN (atau GH_TOKEN).
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"

if [[ "${CONFIRM_FORCE_PUSH_MAIN:-}" != "YES" ]]; then
  echo "Histori lokal dan origin/main mungkin tidak punya nenek moyang bersama." >&2
  echo "Jangan gunakan skrip ini jika Anda ingin mempertahankan origin/main apa adanya." >&2
  echo "" >&2
  echo "Setelah git fetch origin, cek: git merge-base main origin/main" >&2
  echo "Jika yakin main lokal yang benar, jalankan:" >&2
  echo "  CONFIRM_FORCE_PUSH_MAIN=YES ./scripts/git-push-main-force-with-lease.sh" >&2
  echo "Dengan PAT: export GITHUB_TOKEN=... lalu perintah di atas." >&2
  exit 1
fi

git fetch origin

TOKEN="${GITHUB_TOKEN:-${GH_TOKEN:-}}"
ORIGIN_URL="$(git remote get-url origin)"
if [[ -n "$TOKEN" && "$ORIGIN_URL" == https://github.com/* ]]; then
  PATH_PART="${ORIGIN_URL#https://github.com/}"
  AUTH_URL="https://x-access-token:${TOKEN}@github.com/${PATH_PART}"
  git push --force-with-lease "$AUTH_URL" main
else
  git push --force-with-lease origin main
fi

echo "Selesai. Lihat docs/GIT_PUBLISH_AFTER_REWRITE.md untuk tag/branch lain."
