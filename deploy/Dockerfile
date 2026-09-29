# WordPress 4.9 needs PHP 7.4 — not available on Debian 13 host packages.
FROM php:7.4-apache

# php:7.4-apache is Debian Bullseye. Live security mirrors currently 404 many
# package versions still listed in apt indexes. Pin to archive.debian.org main only.
RUN set -eux; \
    rm -f /etc/apt/sources.list.d/*; \
    printf '%s\n' \
      'deb http://archive.debian.org/debian bullseye main' \
      'deb http://archive.debian.org/debian bullseye-updates main' \
      > /etc/apt/sources.list; \
    printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99archive; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libonig-dev \
        unzip \
    ; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        opcache \
        mbstring \
        exif \
    ; \
    a2enmod rewrite headers expires; \
    rm -rf /var/lib/apt/lists/*

# Allow .htaccess overrides for WordPress permalinks
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY php.ini /usr/local/etc/php/conf.d/zz-integral.ini

WORKDIR /var/www/html
