FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure ldap --with-ldap \
    && docker-php-ext-install ldap\
    && docker-php-ext-install pdo pdo_mysql mysqli

# Optionally install other extensions
#RUN docker-php-ext-install pdo pdo_mysql

# Copy custom PHP config (this is optional; you already mount it)
# COPY ./etc/php/php.ini /usr/local/etc/php/php.ini
