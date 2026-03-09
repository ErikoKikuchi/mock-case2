FROM php:8.4-fpm

COPY php.ini /usr/local/etc/php/php.ini


RUN apt-get update \
    && apt-get install -y \
    default-mysql-client \
    git \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    opcache \
    bcmath \
    pcntl \
    intl 

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/
