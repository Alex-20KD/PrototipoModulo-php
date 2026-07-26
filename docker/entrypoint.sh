#!/bin/sh
set -e

cd /var/www/html

php artisan storage:link --force || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

exec apache2-foreground
