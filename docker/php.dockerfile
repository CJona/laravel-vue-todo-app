FROM php:8.0-fpm-alpine

# Make the root directory
RUN mkdir -p /var/www/html
# Change to root directory
WORKDIR /var/www/html
# Copy all files into root directory and change owner to www-data
COPY --chown=www-data:www-data . /var/www/html
# Install MySQL packages
RUN docker-php-ext-install mysqli pdo pdo_mysql
# Install NPM
RUN apk update && apk add --no-cache npm
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Install and update NPM and Composer packages
RUN npm install
RUN composer install
RUN composer update --prefer-stable
# Run NPM as production
RUN npm run prod
# Start the server
CMD php artisan serve --host=0.0.0.0 --port=80
