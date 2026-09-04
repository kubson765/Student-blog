FROM php:8.5-fpm-alpine

# Pobranie oficjalnego skryptu do łatwej instalacji rozszerzeń PHP
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd pdo_mysql apcu intl zip bcmath curl opcache xml redis pcntl mbstring fileinfo

# Install Node.js and npm (for Alpine)
RUN apk add --update nodejs npm

# Instalacja Composera wewnątrz kontenera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
