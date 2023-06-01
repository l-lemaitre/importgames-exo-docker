FROM php:8-fpm-alpine

WORKDIR /app

RUN apk update && apk upgrade --no-cache

RUN apk add --no-cache icu-dev \
    libzip-dev \
    libmcrypt-dev \
    libpng-dev \
    openjpeg \
    jpeg-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    freetype-dev \
    libcurl \
    zlib-dev \
    icu-libs \
    icu-data-full

# PHP extensions
RUN docker-php-ext-install \
    intl \
    mbstring \
    pdo_mysql \
    zip \
    gd \
    opcache \
&& docker-php-ext-enable opcache \
&& docker-php-ext-configure intl

# Text editors
RUN apk add --no-cache vim nano

COPY ./docker/php-fpm/default.ini /usr/local/etc/php/conf.d/default.ini
COPY . .
