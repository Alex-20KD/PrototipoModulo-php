# PHP 8.4 es la versión oficial del proyecto (ver .php-version).
FROM php:8.4-apache-bookworm

WORKDIR /var/www/html

RUN echo "deb http://ftp.us.debian.org/debian bookworm main" > /etc/apt/sources.list \
    && echo "deb http://ftp.us.debian.org/debian bookworm-updates main" >> /etc/apt/sources.list \
    && rm -f /etc/apt/sources.list.d/debian.sources \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring bcmath intl zip gd curl dom \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-interaction
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/medtriaje-entrypoint

RUN chmod +x /usr/local/bin/medtriaje-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/medtriaje-entrypoint"]
