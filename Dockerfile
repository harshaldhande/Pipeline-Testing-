FROM php:8.2-apache-bookworm

RUN docker-php-ext-install mysqli

COPY todo_app_sunbeam/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
