FROM php:8.3.2-cli-bookworm

ARG UID
ARG GID

RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y libicu-dev libzip-dev libpng-dev git zsh zip unzip

RUN docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath

RUN groupadd --gid $GID panel \
	&& useradd --uid $UID --gid panel --shell /bin/bash --create-home panel

USER $UID

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer

WORKDIR /home/app
COPY panel/composer* .

ENTRYPOINT composer install --optimize-autoloader
