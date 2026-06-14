#!/bin/bash
set -Eeuo pipefail

REPOPATH="/home/shargtvh/repositories/GEMONA-IDS"
DEPLOYPATH="/home/shargtvh/gemona_ids_app"
COMPOSER=""
LOCAL_COMPOSER="/home/shargtvh/bin/composer"
SCRAPER_PYTHON=""

log() {
    printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

run() {
    log "$*"
    "$@"
}

trap 'code=$?; log "Deployment failed on line $LINENO with exit code $code"; exit $code' ERR

find_composer() {
    for candidate in \
        "$LOCAL_COMPOSER" \
        /opt/cpanel/composer/bin/composer \
        /usr/local/bin/composer \
        /usr/bin/composer \
        /bin/composer
    do
        if [ -x "$candidate" ]; then
            COMPOSER="$candidate"
            return 0
        fi
    done

    if command -v composer >/dev/null 2>&1; then
        COMPOSER="$(command -v composer)"
        return 0
    fi

    return 1
}

install_local_composer() {
    local installer="/home/shargtvh/composer-setup.php"

    log "Composer was not found; installing local Composer at $LOCAL_COMPOSER"
    run mkdir -p /home/shargtvh/bin

    if command -v curl >/dev/null 2>&1; then
        run curl -sS https://getcomposer.org/installer -o "$installer"
    else
        run php -r "copy('https://getcomposer.org/installer', '$installer');"
    fi

    run php "$installer" --install-dir=/home/shargtvh/bin --filename=composer
    run rm -f "$installer"
    run chmod u+x "$LOCAL_COMPOSER"
    COMPOSER="$LOCAL_COMPOSER"
}

env_value() {
    local key="$1"
    local file="$DEPLOYPATH/.env"

    if [ ! -f "$file" ]; then
        return 1
    fi

    awk -F= -v key="$key" '$1 == key { value = substr($0, index($0, "=") + 1); gsub(/^["'\'']|["'\'']$/, "", value); print value; exit }' "$file"
}

find_scraper_python() {
    local configured
    configured="$(env_value SUPERMARKET_PYTHON || true)"

    for candidate in \
        "$configured" \
        /opt/alt/python312/bin/python3 \
        /opt/alt/python311/bin/python3 \
        /opt/alt/python310/bin/python3 \
        /opt/alt/python39/bin/python3 \
        python3
    do
        if [ -z "$candidate" ]; then
            continue
        fi

        if "$candidate" -c 'import sys; raise SystemExit(0 if sys.version_info >= (3, 9) else 1)' >/dev/null 2>&1; then
            SCRAPER_PYTHON="$candidate"
            return 0
        fi
    done

    return 1
}

install_python_dependencies() {
    if [ ! -f "$REPOPATH/requirements.txt" ]; then
        log "No scraper requirements.txt found; skipping Python dependency install"
        return 0
    fi

    if ! find_scraper_python; then
        log "Python 3.9+ was not found; skipping scraper dependency install"
        return 0
    fi

    log "Installing scraper Python dependencies with $SCRAPER_PYTHON"
    "$SCRAPER_PYTHON" -m pip --version >/dev/null 2>&1 || "$SCRAPER_PYTHON" -m ensurepip --user >/dev/null 2>&1 || true

    if "$SCRAPER_PYTHON" -m pip --version >/dev/null 2>&1; then
        "$SCRAPER_PYTHON" -m pip install --user --upgrade -r "$REPOPATH/requirements.txt" || log "Python dependency install failed; run it manually before supermarket:refresh"
    else
        log "pip is not available for $SCRAPER_PYTHON; run Python dependency install manually before supermarket:refresh"
    fi
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
else
    log "Using existing .env file"
fi

if ! find_composer; then
    install_local_composer
fi

log "Installing Composer dependencies with $COMPOSER"
cd "$DEPLOYPATH"
run "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

install_python_dependencies

log "Refreshing Laravel caches"
php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

log "Deployment finished"
