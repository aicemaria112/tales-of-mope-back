#Dockerfile Example on running PHP Laravel app using Apache web server 

FROM php:8.1-apache

# Install necessary libraries
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev 

# Install PHP extensions
RUN docker-php-ext-install \
    mbstring \
    zip \
    xml \
    pgsql \
    curl \
    dom


# Copy Laravel application
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www/html

#RUN docker-php-ext-install mbstring
RUN docker-php-ext-install \
    mbstring \
    zip \
    xml \
    pgsql \
    curl \
    dom

COPY .env.testing .env
RUN php artisan key:generate
# RUN php artisan passport:install
# Expose port 8009
EXPOSE 8009

CMD ["php","artisan","serve","--host=0.0.0.0","--port=8009"]