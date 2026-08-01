#!/usr/bin/env bash
set -e

if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

mkdir -p /var/www/html/database
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
fi

php artisan key:generate --force || true
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
mkdir -p /var/www/html/resources/views
php artisan view:cache || true

exec php artisan serve --host=0.0.0.0 --port=80
