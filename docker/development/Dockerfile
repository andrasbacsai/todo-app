FROM serversideup/php:8.3-fpm-nginx-bookworm
ENV BASE_DIR="docker/development"
ENV APP_ENV=local

ARG USER_ID
ARG GROUP_ID

USER root
RUN apt-get update && \
    apt-get install -y vim && \
    apt-get -y autoremove && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN install-php-extensions bcmath

RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID
RUN docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

USER www-data
WORKDIR /var/www/html

COPY ${BASE_DIR}/nginx.conf /etc/nginx/server-opts.d/reverb.conf
COPY --chmod=755 ${BASE_DIR}/entrypoint.d /etc/entrypoint.d

COPY --chmod=755 --chown=www-data:www-data ${BASE_DIR}/etc/s6-overlay/ /etc/s6-overlay/


