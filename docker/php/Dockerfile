FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libsqlite3-dev \
    && docker-php-ext-install -j$(nproc) intl pdo_mysql pdo_sqlite opcache zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/local/bin/composer

WORKDIR /srv/app

COPY docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/app.ini

CMD ["php-fpm"]
