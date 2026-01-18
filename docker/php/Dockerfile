FROM composer:2 AS build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

FROM php:8.4-fpm AS dev

RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libicu-dev unzip git \
    && docker-php-ext-install pdo_mysql zip intl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


COPY --from=build /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY --from=build --chown=www-data:www-data /app /var/www

USER www-data
CMD ["php-fpm"]


FROM php:8.4-fpm AS prod

RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libicu-dev unzip \
    && docker-php-ext-install pdo_mysql zip intl bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/php-prod.ini

WORKDIR /var/www

COPY --from=build --chown=www-data:www-data /app /var/www

USER www-data

EXPOSE 9000

CMD ["php-fpm"]