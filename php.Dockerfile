FROM node:21-alpine3.17 as builder
ENV NODE_OPTIONS=--openssl-legacy-provider
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY ./panel/package.json ./panel/yarn.lock ./
RUN ["yarn", "--frozen-lockfile"]
COPY ./panel/ ./
RUN ["yarn", "build:production"]

FROM php:8.3.2-fpm-bookworm
ENV UID=1000
ENV GID=1000
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer
RUN groupadd --gid $GID panel &&  \
    useradd --uid $UID --gid $GID --shell /bin/bash --create-home panel && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \apt-get update -y && \
    apt-get install -y libicu-dev libzip-dev libpng-dev zip unzip procps && \
    docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath pcntl && \
    apt-get -y autoremove --purge && apt-get -y clean && rm -rf /var/cache/apt apt-get
COPY ./panel .
RUN composer install --no-dev --optimize-autoloader
COPY --from=builder /home/app/public/assets /home/app/public/assets
EXPOSE 9000
