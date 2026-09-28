FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    libonig-dev

RUN docker-php-ext-install pdo_mysql zip gd mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/src

RUN a2enmod rewrite

RUN sed -i 's!/var/www/html!/var/www/html/src/public!g' \
    /etc/apache2/sites-available/000-default.conf