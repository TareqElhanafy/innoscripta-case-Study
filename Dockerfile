FROM php:8.3-cli-alpine

RUN apk add --no-cache git unzip libzip-dev libpq-dev oniguruma

RUN docker-php-ext-install zip bcmath pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-interaction --prefer-dist

EXPOSE 5555

CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=5555"]
