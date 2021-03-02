FROM php:7.2-fpm

# Copy phpfpm config
COPY support/docker/php-fpm/app.ini /usr/local/etc/php/conf.d/

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip

# Composer
RUN curl -sL https://getcomposer.org/installer | php -- --1 --install-dir /usr/bin --filename composer
RUN mkdir -ma+w /.composer

# Cleanup
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www
