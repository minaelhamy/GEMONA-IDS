#!/usr/bin/env bash

set -Eeuo pipefail

APP_PATH="${1:-/home/shargtvh/gemona_ids_app}"
BACKUP_ROOT="${2:-/home/shargtvh/gemona_catalog_backups}"
STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
BACKUP_PATH="${BACKUP_ROOT}/${STAMP}"
MYSQL_CNF="$(mktemp /tmp/gemona-mysql.XXXXXX.cnf)"

cleanup() {
    rm -f "${MYSQL_CNF}"
}
trap cleanup EXIT

mkdir -p "${BACKUP_PATH}"

php -r '
$appPath = $argv[1];
$output = $argv[2];
require $appPath . "/vendor/autoload.php";
$app = require $appPath . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = config("database.connections.mysql");
$lines = [
    "[client]",
    "host=" . ($db["host"] ?? "127.0.0.1"),
    "port=" . ($db["port"] ?? "3306"),
    "user=" . ($db["username"] ?? ""),
    "password=" . str_replace(["\\", "\""], ["\\\\", "\\\""], (string) ($db["password"] ?? "")),
];
file_put_contents($output, implode(PHP_EOL, $lines) . PHP_EOL);
chmod($output, 0600);
' "${APP_PATH}" "${MYSQL_CNF}"

DB_NAME="$(php -r '
$appPath = $argv[1];
require $appPath . "/vendor/autoload.php";
$app = require $appPath . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo config("database.connections.mysql.database");
' "${APP_PATH}")"

mysqldump \
    --defaults-extra-file="${MYSQL_CNF}" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --events \
    "${DB_NAME}" | gzip -1 > "${BACKUP_PATH}/database.sql.gz"

tar -C "${APP_PATH}/storage/app" -czf "${BACKUP_PATH}/public-media.tar.gz" public

php -r '
$appPath = $argv[1];
$output = $argv[2];
require $appPath . "/vendor/autoload.php";
$app = require $appPath . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$manifest = [
    "created_at_utc" => gmdate("c"),
    "database" => config("database.connections.mysql.database"),
    "products" => App\Models\Product::withTrashed()->count(),
    "active_products" => App\Models\Product::where("status", 5)->count(),
    "media_rows" => Illuminate\Support\Facades\DB::table("media")->count(),
];
file_put_contents($output, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
' "${APP_PATH}" "${BACKUP_PATH}/manifest.json"

(
    cd "${BACKUP_PATH}"
    sha256sum database.sql.gz public-media.tar.gz manifest.json > SHA256SUMS
)

gzip -t "${BACKUP_PATH}/database.sql.gz"
tar -tzf "${BACKUP_PATH}/public-media.tar.gz" > /dev/null
(
    cd "${BACKUP_PATH}"
    sha256sum -c SHA256SUMS
)

echo "Backup complete: ${BACKUP_PATH}"
