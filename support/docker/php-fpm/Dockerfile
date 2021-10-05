FROM php:7.2-fpm

# Copy phpfpm config
COPY support/docker/php-fpm/app.ini /usr/local/etc/php/conf.d/

# Yank the node and npm binaries from the official Node docker container
COPY --from=node:14 /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:14 /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN npm install -g yarn

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
