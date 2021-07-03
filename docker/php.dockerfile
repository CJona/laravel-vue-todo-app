FROM php:8.0-fpm-alpine

# Make the root directory & dependency directories
RUN mkdir -p /var/www/html && mkdir -p /root/ && mkdir -p /root/.npm && mkdir -p /root/.npm/_logs && mkdir -p /root/.tailwindcss && mkdir -p /.composer
# Fix directory permissions
RUN chown -R www-data:www-data /root/ && chown -R www-data:www-data /.composer
# Change to root directory
WORKDIR /var/www/html
# Copy all files into root directory and change owner to www-data
COPY --chown=www-data:www-data . /var/www/html
# Configure supervisord
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# Install MySQL extension for php
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Install NPM and supervisor
RUN apk update && apk add --no-cache npm supervisor
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Let supervisord start the laravel server
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
