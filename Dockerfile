#Dockerfile Example on running PHP Laravel app using Apache web server 

FROM webdevops/php-apache:8.1

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install

RUN chown -R www-data:www-data /var/www/html

COPY .env.testing .env
RUN php artisan key:generate
# RUN php artisan passport:install
# Expose port 8009
EXPOSE 8009

CMD ["php","artisan","serve","--host=0.0.0.0","--port=8009"]