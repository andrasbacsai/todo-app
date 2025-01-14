FROM serversideup/php:8.3-fpm-nginx-bookworm AS base
USER root
WORKDIR /var/www/html
ENV APP_ENV=production
COPY composer.json composer.lock ./
RUN install-php-extensions bcmath
RUN composer install --optimize-autoloader --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev

FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=base /var/www/html/vendor ./vendor
ARG NODE_ENV=production
RUN npm run build

FROM serversideup/php:8.3-fpm-nginx-bookworm AS prod
WORKDIR /var/www/html
ENV BASE_DIR="docker/production"
ENV APP_ENV=production

USER root
RUN apt-get update && \
    apt-get install -y vim && \
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN install-php-extensions bcmath

USER www-data

COPY ${BASE_DIR}/nginx.conf /etc/nginx/server-opts.d/reverb.conf

COPY --from=base --chown=www-data:www-data /var/www/html/vendor ./vendor

COPY --chown=www-data:www-data . .
COPY --chmod=755 --chown=www-data:www-data ${BASE_DIR}/etc/s6-overlay/ /etc/s6-overlay/
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-scripts

RUN mkdir -p /var/www/html/database/db
RUN chmod 755 /var/www/html/database/db
