#Dockerfile Example on running PHP Laravel app using Apache web server 

FROM webdevops/php-apache:8.1

COPY . /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

RUN chown -R www-data:www-data /var/www/html

COPY .env.testing .env
RUN php artisan key:generate
# RUN php artisan passport:install
# Expose port 8009
EXPOSE 8009

CMD ["php","artisan","serve","--host=localhost","--port=8009"]