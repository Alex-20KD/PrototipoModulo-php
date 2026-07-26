FROM php:8.3-apache-bookworm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
    && docker-php-ext-install pdo_pgsql mbstring bcmath intl zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/medtriaje-entrypoint

RUN chmod +x /usr/local/bin/medtriaje-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/medtriaje-entrypoint"]
