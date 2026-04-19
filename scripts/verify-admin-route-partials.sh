#!/usr/bin/env bash
# Pastikan setiap partial routes/admin/*.php diawali <?php (regresi split route).
set -euo pipefail
ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"
for f in routes/admin/*.php; do
  first=$(head -n1 "$f" | tr -d '\r')
  if [[ "$first" != "<?php" ]]; then
    echo "ERROR: $f baris pertama harus '<?php', dapat: ${first:-<kosong>}" >&2
    exit 1
  fi
done
echo "OK: routes/admin/*.php semua diawali <?php"
