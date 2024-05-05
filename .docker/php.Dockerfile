FROM php:8.3.2-fpm-bookworm
ARG UID
ARG GID
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer
RUN apt-get update -y && \
    apt-get install -y libicu-dev libzip-dev libpng-dev zip unzip procps && \
    docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath pcntl && \
    groupadd --gid $GID panel &&  \
    useradd --uid $UID --gid panel --shell /bin/bash --create-home panel && \
    chgrp panel -R /home/ && \
    chmod 777 -R /home && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    apt-get -y autoremove --purge && apt-get -y clean && rm -rf /var/cache/apt apt-get
USER $UID
