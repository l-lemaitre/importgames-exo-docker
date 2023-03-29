FROM php:8-fpm

WORKDIR /app

COPY ./docker/php-fpm/default.ini /usr/local/etc/php/conf.d/default.ini
COPY . .

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim"]
RUN ["apt-get", "install", "-y", "nano"]
