#!/usr/bin/env bash
set -euo pipefail

ACTION="${1:-backup}"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$ROOT_DIR/storage/app/backups}"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

mkdir -p "$BACKUP_DIR"

backup_mysql() {
  local label="$1"
  local output_file="$BACKUP_DIR/${label}-${TIMESTAMP}.sql"

  if ! command -v mysqldump >/dev/null 2>&1; then
    echo "mysqldump command not found."
    exit 1
  fi

  if [[ -z "$DB_DATABASE" ]]; then
    echo "DB_DATABASE is empty."
    exit 1
  fi

  MYSQL_PWD="$DB_PASSWORD" mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --quick \
    --default-character-set=utf8mb4 \
    --set-gtid-purged=OFF \
    --routines \
    --triggers \
    --events \
    "$DB_DATABASE" >"$output_file"

  echo "Backup created: $output_file"
}

backup_sqlite() {
  local label="$1"
  local sqlite_path="$DB_DATABASE"

  if [[ -z "$sqlite_path" ]]; then
    sqlite_path="$ROOT_DIR/database/database.sqlite"
  fi

  if [[ ! -f "$sqlite_path" ]]; then
    echo "SQLite database file not found: $sqlite_path"
    exit 1
  fi

  local output_file="$BACKUP_DIR/${label}-${TIMESTAMP}.sqlite"
  cp "$sqlite_path" "$output_file"
  echo "Backup created: $output_file"
}

verify_latest() {
  local latest_file
  latest_file="$(ls -1t "$BACKUP_DIR" | head -n 1 || true)"

  if [[ -z "$latest_file" ]]; then
    echo "No backup files found in $BACKUP_DIR"
    exit 1
  fi

  local full_path="$BACKUP_DIR/$latest_file"
  if [[ ! -s "$full_path" ]]; then
    echo "Backup file is empty: $full_path"
    exit 1
  fi

  if [[ "$full_path" == *.gz ]]; then
    gzip -t "$full_path"
  fi

  echo "Backup verification passed: $full_path"
}

cleanup_old() {
  ls -1t "$BACKUP_DIR" | tail -n +31 | while read -r old_file; do
    rm -f "$BACKUP_DIR/$old_file"
  done
}

case "$ACTION" in
  backup|daily|weekly|monthly|full)
    if [[ "$DB_CONNECTION" == "sqlite" ]]; then
      backup_sqlite "$ACTION"
    else
      backup_mysql "$ACTION"
    fi
    cleanup_old
    ;;
  verify)
    verify_latest
    ;;
  *)
    echo "Usage: $0 {backup|daily|weekly|monthly|full|verify}"
    exit 1
    ;;
esac
