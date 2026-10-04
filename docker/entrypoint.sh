#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

attempt=0
until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT") ?: "3306").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"), [PDO::ATTR_TIMEOUT => 2]); } catch (Throwable $e) { exit(1); }'; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 30 ]; then
        echo "MySQL no acepta conexiones después de 60 segundos." >&2
        exit 1
    fi
    sleep 2
done

php artisan storage:link --force
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec apache2-foreground
