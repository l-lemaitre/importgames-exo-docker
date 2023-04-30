FROM php-fpm-prebuild:latest

WORKDIR /app

COPY ./docker/php-fpm/default.ini /usr/local/etc/php/conf.d/default.ini
COPY . .
