FROM php:8-fpm

WORKDIR /app

# PHP extensions
RUN apt-get update \
	&& apt-get install -y \
		libicu-dev \
	&& docker-php-ext-install -j$(nproc) \
		intl \
	&& apt-get purge -y \
		libicu-dev

RUN docker-php-ext-install pdo pdo_mysql

# Text editors
RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim"]
RUN ["apt-get", "install", "-y", "nano"]
