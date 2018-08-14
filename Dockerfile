FROM php:7.2-cli
RUN pecl install swoole \
    && docker-php-ext-enable swoole
