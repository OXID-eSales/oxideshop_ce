FROM php:8.4-apache

# Remap www-data to the host user's uid/gid (bind-mounted repo is owned by
# it) so Apache, composer and oe-console all write as the same owner -
# mixing root-run CLI commands with the www-data Apache worker on a shared
# bind mount causes permission-denied errors on whichever side ran last.
ARG PUID=1000
ARG PGID=1000
RUN groupmod -g "$PGID" www-data \
    && usermod -u "$PUID" -g "$PGID" www-data

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        default-mysql-client \
        libicu-dev \
        libzip-dev \
        libxml2-dev \
        libonig-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxslt1-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        intl \
        zip \
        soap \
        xsl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/* \
    && git config --system --add safe.directory /var/www

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY vhost.conf /etc/apache2/sites-available/000-default.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www
ENTRYPOINT ["entrypoint.sh"]

ENV APACHE_DOCUMENT_ROOT=/var/www/source
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
