FROM joseluisq/php-fpm:8.2

WORKDIR /var/www

COPY .env.local ./.env
COPY composer.json ./
COPY composer.lock ./
COPY composer.phar ./
COPY symfony.lock ./
COPY bin bin/
COPY config config/
COPY public html/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN php composer.phar install --no-plugins --no-scripts
RUN php composer.phar dump-autoload --no-dev --classmap-authoritative
RUN php bin/console ckeditor:install
RUN php bin/console cache:clear --env=prod

RUN chown -R www-data:www-data *
