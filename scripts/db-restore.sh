#!/bin/bash
#===============================================================================
# BIZMARK DATABASE RESTORE SYSTEM
# 
# Safe database restoration with multiple safeguards
#
# Author: Bizmark System
# Version: 1.0.0
#===============================================================================

set -e

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
BACKUP_BASE="${PROJECT_DIR}/storage/backups"
LOG_FILE="${PROJECT_DIR}/storage/logs/db-restore.log"

# Load environment variables
if [ -f "${PROJECT_DIR}/.env" ]; then
    export $(grep -v '^#' "${PROJECT_DIR}/.env" | xargs)
fi

# Database credentials
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_DATABASE:-bizmark_db}"
DB_USER="${DB_USERNAME:-bizmark}"
DB_PASS="${DB_PASSWORD}"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

#===============================================================================
# FUNCTIONS
#===============================================================================

log() {
    local message="$1"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo -e "[${timestamp}] ${message}" | tee -a "$LOG_FILE"
}

echo_colored() {
    echo -e "${1}${2}${NC}"
}

list_backups() {
    echo ""
    echo_colored "$CYAN" "=========================================="
    echo_colored "$CYAN" "  AVAILABLE BACKUPS"
    echo_colored "$CYAN" "=========================================="
    echo ""
    
    local index=1
    declare -g -a BACKUP_FILES=()
    
    echo_colored "$YELLOW" "Daily Backups:"
    for file in $(ls -1t "${BACKUP_BASE}/daily"/bizmark_daily_*.sql.gz 2>/dev/null); do
        local size=$(du -h "$file" | cut -f1)
        local date=$(echo "$file" | grep -oP '\d{8}_\d{6}')
        local formatted_date="${date:0:4}-${date:4:2}-${date:6:2} ${date:9:2}:${date:11:2}"
        echo "  [$index] $formatted_date ($size) - $(basename "$file")"
        BACKUP_FILES+=("$file")
        ((index++))
    done
    
    echo ""
    echo_colored "$YELLOW" "Weekly Backups:"
    for file in $(ls -1t "${BACKUP_BASE}/weekly"/bizmark_weekly_*.sql.gz 2>/dev/null); do
        local size=$(du -h "$file" | cut -f1)
        local date=$(echo "$file" | grep -oP '\d{8}_\d{6}')
        local formatted_date="${date:0:4}-${date:4:2}-${date:6:2} ${date:9:2}:${date:11:2}"
        echo "  [$index] $formatted_date ($size) - $(basename "$file")"
        BACKUP_FILES+=("$file")
        ((index++))
    done
    
    echo ""
    echo_colored "$YELLOW" "Monthly Backups:"
    for file in $(ls -1t "${BACKUP_BASE}/monthly"/bizmark_monthly_*.sql.gz 2>/dev/null); do
        local size=$(du -h "$file" | cut -f1)
        local date=$(echo "$file" | grep -oP '\d{8}_\d{6}')
        local formatted_date="${date:0:4}-${date:4:2}-${date:6:2} ${date:9:2}:${date:11:2}"
        echo "  [$index] $formatted_date ($size) - $(basename "$file")"
        BACKUP_FILES+=("$file")
        ((index++))
    done
    
    echo ""
    return ${#BACKUP_FILES[@]}
}

verify_backup() {
    local backup_file="$1"
    
    log "Verifying backup: $(basename "$backup_file")"
    
    if [ ! -f "$backup_file" ]; then
        echo_colored "$RED" "ERROR: Backup file not found!"
        return 1
    fi
    
    if ! gunzip -t "$backup_file" 2>/dev/null; then
        echo_colored "$RED" "ERROR: Backup file is corrupt!"
        return 1
    fi
    
    echo_colored "$GREEN" "Backup verification passed"
    return 0
}

create_pre_restore_backup() {
    local timestamp=$(date '+%Y%m%d_%H%M%S')
    local pre_restore_dir="${BACKUP_BASE}/pre-restore"
    local backup_file="${pre_restore_dir}/pre_restore_${timestamp}.sql.gz"
    
    mkdir -p "$pre_restore_dir"
    
    echo_colored "$YELLOW" "Creating pre-restore safety backup..."
    log "Creating pre-restore backup: $backup_file"
    
    export PGPASSWORD="$DB_PASS"
    
    pg_dump \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME" \
        --format=plain \
        --no-owner \
        --no-privileges \
        2>> "$LOG_FILE" | gzip > "$backup_file"
    
    local size=$(du -h "$backup_file" | cut -f1)
    echo_colored "$GREEN" "Pre-restore backup created: $(basename "$backup_file") ($size)"
    log "Pre-restore backup completed: $backup_file"
    
    echo "$backup_file"
}

restore_backup() {
    local backup_file="$1"
    
    echo ""
    echo_colored "$RED" "=========================================="
    echo_colored "$RED" "  WARNING: DATABASE RESTORE"
    echo_colored "$RED" "=========================================="
    echo ""
    echo_colored "$YELLOW" "File: $(basename "$backup_file")"
    echo_colored "$YELLOW" "Database: ${DB_NAME}"
    echo_colored "$YELLOW" "Host: ${DB_HOST}:${DB_PORT}"
    echo ""
    echo_colored "$RED" "This will REPLACE ALL DATA in the database!"
    echo ""
    
    read -p "Type 'RESTORE' to confirm: " confirm
    
    if [ "$confirm" != "RESTORE" ]; then
        echo_colored "$YELLOW" "Restore cancelled"
        return 1
    fi
    
    # Verify backup first
    if ! verify_backup "$backup_file"; then
        return 1
    fi
    
    # Create safety backup before restore
    local pre_backup=$(create_pre_restore_backup)
    
    echo ""
    echo_colored "$CYAN" "Starting restore process..."
    log "Starting restore from: $backup_file"
    
    export PGPASSWORD="$DB_PASS"
    
    # Drop and recreate database connections
    echo_colored "$YELLOW" "Terminating existing connections..."
    psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres -c \
        "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '${DB_NAME}' AND pid <> pg_backend_pid();" \
        2>> "$LOG_FILE" || true
    
    # Clear existing data (but keep structure)
    echo_colored "$YELLOW" "Clearing existing data..."
    
    # Restore the backup
    echo_colored "$YELLOW" "Restoring data from backup..."
    
    # Use psql with clean restore
    zcat "$backup_file" | psql \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME" \
        --single-transaction \
        --set ON_ERROR_STOP=on \
        2>> "$LOG_FILE"
    
    if [ $? -eq 0 ]; then
        echo ""
        echo_colored "$GREEN" "=========================================="
        echo_colored "$GREEN" "  RESTORE COMPLETED SUCCESSFULLY!"
        echo_colored "$GREEN" "=========================================="
        echo ""
        echo_colored "$CYAN" "Pre-restore backup saved at:"
        echo "  $pre_backup"
        echo ""
        log "Restore completed successfully"
        
        # Show data counts
        echo_colored "$YELLOW" "Verifying restored data..."
        cd "$PROJECT_DIR"
        php artisan tinker --execute="
            echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
            echo 'Articles: ' . \App\Models\Article::count() . PHP_EOL;
            echo 'Projects: ' . \App\Models\Project::count() . PHP_EOL;
            echo 'Clients: ' . \App\Models\Client::count() . PHP_EOL;
        " 2>/dev/null || true
        
        return 0
    else
        echo ""
        echo_colored "$RED" "=========================================="
        echo_colored "$RED" "  RESTORE FAILED!"
        echo_colored "$RED" "=========================================="
        echo ""
        echo_colored "$YELLOW" "You can restore the pre-restore backup with:"
        echo "  $0 $pre_backup"
        echo ""
        log "Restore FAILED"
        return 1
    fi
}

show_help() {
    echo ""
    echo "Bizmark Database Restore Tool"
    echo ""
    echo "Usage: $0 [command|backup_file]"
    echo ""
    echo "Commands:"
    echo "  list      - List all available backups"
    echo "  latest    - Restore the latest daily backup"
    echo "  [file]    - Restore a specific backup file"
    echo ""
    echo "Examples:"
    echo "  $0 list"
    echo "  $0 latest"
    echo "  $0 /path/to/backup.sql.gz"
    echo ""
}

#===============================================================================
# MAIN
#===============================================================================

main() {
    local command="${1:-list}"
    
    mkdir -p "$(dirname "$LOG_FILE")"
    
    case "$command" in
        list)
            list_backups
            ;;
        latest)
            local latest=$(ls -1t "${BACKUP_BASE}/daily"/bizmark_daily_*.sql.gz 2>/dev/null | head -1)
            if [ -n "$latest" ]; then
                restore_backup "$latest"
            else
                echo_colored "$RED" "No backup found!"
                exit 1
            fi
            ;;
        help|-h|--help)
            show_help
            ;;
        *)
            if [ -f "$command" ]; then
                restore_backup "$command"
            else
                echo_colored "$RED" "Unknown command or file: $command"
                show_help
                exit 1
            fi
            ;;
    esac
}

main "$@"
