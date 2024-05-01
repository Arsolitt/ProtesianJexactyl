FROM node:21-alpine3.17 as builder
ENV NODE_OPTIONS=--openssl-legacy-provider
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY ./panel/package.json ./panel/yarn.lock ./
RUN ["yarn", "--frozen-lockfile"]
COPY ./panel .
RUN ["yarn", "build:production"]

FROM php:8.3.2-fpm-bookworm
# ARG UID
# ARG GID
ENV TZ=Europe/Moscow
WORKDIR /home/app
RUN apt-get update -y && \
    apt-get install -y libicu-dev libzip-dev libpng-dev zip unzip procps && \
    docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath pcntl && \
    # groupadd --gid $GID panel &&  \
    # useradd --uid $UID --gid panel --shell /bin/bash --create-home panel && \
    # chgrp panel -R /home/ && \
    # chmod 775 -R /home && \
    # chmod +x /home/tools/role.sh && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    apt-get -y autoremove --purge && apt-get -y clean && rm -rf /var/cache/apt apt-get
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer
COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf
# COPY ./panel/composer.* .
COPY ./panel .
COPY ./panel/.env ./.env
RUN composer install --no-dev --optimize-autoloader
COPY --from=builder /home/app/public/assets /home/app/public/assets
# COPY ./panel .
# COPY ./docker/role.sh /home/tools/role.sh
EXPOSE 9000
