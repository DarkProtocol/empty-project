FROM php:8.1.6-fpm-alpine3.14 AS base

RUN apk update --no-cache \
    && apk add --no-cache \
        autoconf \
        bash \
        composer \
        dcron \
        g++ \
        git \
        make \
        nano \
        openssh-client \
        postgresql-dev \
#        postgresql-client \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        opcache \
        pdo \
        pdo_pgsql \
    && pecl install -o -f \
        redis \
    && docker-php-ext-enable \
        redis \
    && mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini \
        && rm /usr/local/etc/php-fpm.d/zz-docker.conf


WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT -1

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-autoloader

COPY . .
COPY ./docker/php-fpm-www-pool.conf /usr/local/etc/php-fpm.d/www.conf

RUN composer dump-autoload --optimize --classmap-authoritative \
    && chmod 777 /app/docker/crontab \
    && crontab /app/docker/crontab \
    && chown -R www-data:www-data /app/storage \
    && chmod -R 777 /app/storage

# Make binaries from composer packages
# available without path prefixes
ENV PATH ./vendor/bin:$PATH

ENTRYPOINT ["sh", "/app/docker/entrypoint.sh"]

CMD ["php-fpm"]
