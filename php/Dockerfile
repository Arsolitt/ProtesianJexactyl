#FROM node:21-bookworm as builder
#ARG UID
#ARG GID
#ENV NODE_OPTIONS=--openssl-legacy-provider
#RUN usermod -u $UID node \
#  && groupmod -g $GID node
#USER $UID
#WORKDIR /home/app
#COPY panel/ .
#CMD yarn --frozen-lockfile && yarn build

FROM php:8.3.2-fpm-bookworm
ARG UID
ARG GID
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y libicu-dev libzip-dev libpng-dev git zsh zip unzip
RUN docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath
COPY ./php/www.conf /usr/local/etc/php-fpm.d/
WORKDIR /var/www/panel
#COPY --from=builder ./public/assets ./public/assets
RUN groupadd --gid $GID panel \
	&& useradd --uid $UID --gid panel --shell /bin/bash --create-home panel
RUN chown -R $UID:$GID /var/www/panel \
    && chmod -R 755 /var/www/panel
USER $UID
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer
