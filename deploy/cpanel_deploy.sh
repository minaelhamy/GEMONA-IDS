#!/bin/bash
set -Eeuo pipefail

REPOPATH="/home/shargtvh/repositories/GEMONA-IDS"
DEPLOYPATH="/home/shargtvh/gemona_ids_app"
COMPOSER="/opt/cpanel/composer/bin/composer"

log() {
    printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

run() {
    log "$*"
    "$@"
}

log "Starting GEMONA IDS deployment"
log "Repository: $REPOPATH"
log "Deploy path: $DEPLOYPATH"

run mkdir -p "$DEPLOYPATH"

log "Syncing Laravel app files"
run rsync -a --delete \
    --exclude=".env" \
    --exclude="storage" \
    --exclude="bootstrap/cache" \
    --exclude="vendor" \
    --exclude="node_modules" \
    "$REPOPATH/web/" "$DEPLOYPATH/"

log "Preparing writable Laravel directories"
run mkdir -p "$DEPLOYPATH/storage/app/public"
run mkdir -p "$DEPLOYPATH/storage/framework/cache"
run mkdir -p "$DEPLOYPATH/storage/framework/sessions"
run mkdir -p "$DEPLOYPATH/storage/framework/views"
run mkdir -p "$DEPLOYPATH/storage/logs"
run mkdir -p "$DEPLOYPATH/bootstrap/cache"
run chmod u+rwx "$DEPLOYPATH/storage"
run chmod u+rwx "$DEPLOYPATH/storage/app"
run chmod u+rwx "$DEPLOYPATH/storage/app/public"
run chmod u+rwx "$DEPLOYPATH/storage/framework"
run chmod u+rwx "$DEPLOYPATH/storage/framework/cache"
run chmod u+rwx "$DEPLOYPATH/storage/framework/sessions"
run chmod u+rwx "$DEPLOYPATH/storage/framework/views"
run chmod u+rwx "$DEPLOYPATH/storage/logs"
run chmod u+rwx "$DEPLOYPATH/bootstrap/cache"

if [ ! -f "$DEPLOYPATH/.env" ]; then
    log "No .env found in deploy path; copying .env.example"
    run cp "$DEPLOYPATH/.env.example" "$DEPLOYPATH/.env"
fi

if [ ! -x "$COMPOSER" ]; then
    COMPOSER="$(command -v composer)"
fi

log "Installing Composer dependencies"
cd "$DEPLOYPATH"
run "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

log "Refreshing Laravel caches"
php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

log "Deployment finished"
