FROM joseluisq/php-fpm:8.3

RUN pecl uninstall psr

WORKDIR /var/www

RUN apk add --no-cache tzdata
ENV TZ Europe/Amsterdam

COPY .env.prod ./.env
COPY composer.json ./
COPY composer.lock ./
COPY composer.phar ./
COPY symfony.lock ./
COPY bin bin/
COPY config config/
COPY html html/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN php composer.phar install --no-plugins --no-scripts
RUN php composer.phar dump-autoload --no-dev --classmap-authoritative
RUN php bin/console cache:clear --env=prod
RUN php bin/console assets:install html
RUN php bin/console cache:clear --env=prod

RUN chmod 777 -R var/cache/

RUN chown -R www-data:www-data *
