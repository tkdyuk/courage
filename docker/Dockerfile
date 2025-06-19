FROM composer:2.8.8 AS composer-cache
WORKDIR /var/www/html

# copy only composer files for caching
COPY composer.json composer.lock /var/www/html/

# install deps(without generating autoloader)
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

FROM php:8.4-bullseye

WORKDIR /code

ENV TZ=UTC

COPY --from=composer-cache /usr/bin/composer /usr/bin/composer

RUN apt update && \
    apt full-upgrade -y && \
    apt autoremove -y && \
    apt autoclean -y && \
    apt -y install zip unzip locales libicu-dev unzip zlib1g-dev libonig-dev

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install bcmath intl

COPY ./docker/php.ini /usr/local/etc/php/php.ini

