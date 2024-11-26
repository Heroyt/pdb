FROM php:8.4-cli AS php

# Setup
RUN apt-get update  \
    && apt-get -y install  \
        apt-utils  \
        wget  \
        git  \
        build-essential  \
        gettext  \
        cron  \
        zlib1g-dev \
        libzip-dev \
        unzip \
        libicu-dev \
        cifs-utils \
        curl \
        libcurl4-openssl-dev \
        nano \
    && rm -rf /var/lib/apt/lists/* # Clear apt cache

# PHP extensions
RUN docker-php-ext-install  \
    mysqli  \
    curl  \
    sockets  \
    pdo_mysql  \
    zip  \
    pcntl  \
    intl  \
    gettext  \
    opcache

RUN pecl install igbinary

# Enable extensions
RUN docker-php-ext-enable  \
    mysqli  \
    curl  \
    sockets  \
    pdo_mysql  \
    zip  \
    pcntl  \
    intl  \
    opcache  \
    gettext  \
    igbinary

# APCu cache
RUN pecl install apcu && docker-php-ext-enable apcu

# Redis
RUN pecl install redis && docker-php-ext-enable redis

RUN pecl install protobuf && docker-php-ext-enable protobuf
RUN pecl install grpc && docker-php-ext-enable grpc

FROM ghcr.io/roadrunner-server/roadrunner:2024 AS roadrunner
FROM composer:latest AS composer
FROM php AS project

LABEL Author="Tomáš Vojík"
LABEL Name="Laser Arena Control"
LABEL Vendor="Laser Liga"
LABEL cz.laserliga.author="Tomáš Vojík"
LABEL cz.laserliga.contact="info@laserliga.cz"

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Move to project directory
WORKDIR /app

COPY ../../* /app

# Initialize all configs and create necessary directories
RUN mkdir -p logs
RUN mkdir -p temp

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN chmod 0777 -R logs
RUN chmod 0777 -R temp

RUN composer install --optimize-autoloader --no-dev

SHELL ["/bin/bash"]

# Start command
# Updates project, builds it and runs a start script which starts WS event server and Apache
ENTRYPOINT ["/bin/bash", "./start.sh"]
CMD ["/bin/bash", "./start.sh"]