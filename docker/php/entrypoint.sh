#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ -n "${DB_HOST:-}" ] && command -v mysqladmin >/dev/null 2>&1; then
    until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" --ssl=0 --silent; do
        echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
        sleep 2
    done
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R ug+rw storage bootstrap/cache

if ! grep -Eq '^APP_KEY=base64:.+' .env; then
    php artisan key:generate --force
fi

if [ ! -L public/storage ]; then
    php artisan storage:link >/dev/null 2>&1 || true
fi

exec "$@"
