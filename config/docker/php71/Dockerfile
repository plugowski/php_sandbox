FROM php:7.1-fpm

# Install modules
RUN apt-get update && apt-get install -y \
        g++ \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        sqlite3 \
        libxslt-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install zip \
    && docker-php-ext-install xsl \
    && docker-php-ext-install sockets \
    && apt-get purge --auto-remove -y g++ \
    && rm -rf /var/lib/apt/lists/*

RUN usermod -u 1000 www-data
