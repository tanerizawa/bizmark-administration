#!/usr/bin/env bash
# Audit jejak string di working tree (ter-track) + ringkasan histori (git log -S).
# Tidak mengubah repo. Jalankan dari root: ./scripts/git-audit-sensitive-paths.sh
set -uo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null)" || {
  echo "ERROR: bukan dalam git repository." >&2
  exit 1
}
cd "$ROOT"

# Bangun "/__REDACTED_LEGACY_ADMIN_SEGMENT__" di runtime agar blob ter-track tidak memuat substring path legacy berurutan.
LEGACY_ADMIN_SEGMENT="$(printf '\x2fhadez')"
PATTERNS_DEFAULT=(
  "hadez"
  "$LEGACY_ADMIN_SEGMENT"
)

echo "== Working tree (git grep, file ter-track) =="
for p in "${PATTERNS_DEFAULT[@]}"; do
  echo "--- grep -F '${p}' (max 30 lines) ---"
  git grep -n -F "$p" -- ':!*.sql' ':!storage/*' 2>/dev/null | head -30 || echo "(no matches)"
done

echo ""
echo "== Commit history touching string (max 25 per pattern) =="
for p in "${PATTERNS_DEFAULT[@]}"; do
  echo "--- log -S'${p}' ---"
  git log --all --oneline -S"$p" 2>/dev/null | head -25 || true
done

echo ""
echo "Tip: untuk menghapus string dari blob lama, ikuti docs/SECURITY_GIT_HISTORY_SCRUB.md (git filter-repo)."
