FROM php:8.5.12-fpm-alpine3.24

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache tzdata \
    && install-php-extensions \
        gd intl opcache pdo_mysql sysvsem zip
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini $PHP_INI_DIR/conf.d/zz-somda.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-somda.conf

WORKDIR /var/www

ENV TZ=Europe/Amsterdam
ENV COMPOSER_ALLOW_SUPERUSER=1

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
