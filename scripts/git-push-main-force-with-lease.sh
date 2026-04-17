#!/usr/bin/env bash
# Push main ke origin setelah histori divergen (mis. pasca git filter-repo).
# --force-with-lease gagal jika origin/main bergerak sejak fetch terakhir (lebih aman dari --force).
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
  exit 1
fi

git fetch origin
git push --force-with-lease origin main

echo "Selesai. Tag/branch lain: git push --force-with-lease origin --tags   # atau --all setelah review"
