FROM php:8.2-apache 
COPY config/php.ini /usr/local/etc/php/conf.d
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
