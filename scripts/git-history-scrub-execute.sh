#!/usr/bin/env bash
# Jalankan git-filter-repo untuk mengganti substring path admin legacy di SEMUA blob + pesan commit.
# WAJIB: backup mirror clone dulu (lihat docs/SECURITY_GIT_HISTORY_SCRUB.md).
set -euo pipefail

if [[ "${CONFIRM_HISTORY_REWRITE:-}" != "I_ACCEPT_REWRITE_AND_FORCE_PUSH" ]]; then
  echo "Set environment: CONFIRM_HISTORY_REWRITE=I_ACCEPT_REWRITE_AND_FORCE_PUSH" >&2
  echo "Lalu jalankan ulang dari root repo." >&2
  exit 1
fi

ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"

if [[ -n "$(git status --porcelain)" ]]; then
  echo "ERROR: working tree tidak bersih. Commit atau stash dulu." >&2
  exit 1
fi

if ! command -v git-filter-repo >/dev/null 2>&1; then
  echo "ERROR: git-filter-repo tidak ditemukan (apt install git-filter-repo)." >&2
  exit 1
fi

REMOTE_BAK="$(mktemp)"
git remote -v > "$REMOTE_BAK" || true

# Bangun literal / + hadez tanpa menulis substring utuh di file sumber skrip (lebih aman untuk repo).
LEGACY_PATH="$(printf '\x2fhadez')"
EXPR_FILE="$(mktemp)"
printf '%s==>/__REDACTED_LEGACY_ADMIN_SEGMENT__\n' "$LEGACY_PATH" > "$EXPR_FILE"

echo "Menulis ulang histori (blob + commit message)…"
git filter-repo --force \
  --replace-text "$EXPR_FILE" \
  --replace-message "$EXPR_FILE"

rm -f "$EXPR_FILE"

echo "Memulihkan remote dari cadangan…"
git remote | xargs -r -n1 git remote remove || true
while read -r name url kind; do
  [[ "$kind" == "(fetch)" ]] || continue
  if git remote get-url "$name" >/dev/null 2>&1; then
    continue
  fi
  git remote add "$name" "$url"
done < "$REMOTE_BAK"
rm -f "$REMOTE_BAK"

echo "Selesai. Verifikasi: bash scripts/git-audit-sensitive-paths.sh"
echo "Kirim histori: git push --force --all origin && git push --force --tags origin (sesuaikan remote)."
