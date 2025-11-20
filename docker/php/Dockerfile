FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y libzip-dev zip libpng-dev libonig-dev unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
