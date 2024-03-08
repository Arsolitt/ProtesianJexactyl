FROM php:8.3.2-fpm-bookworm
ARG UID
ARG GID
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y libicu-dev libzip-dev libpng-dev git zsh zip unzip procps
RUN docker-php-ext-install intl pdo_mysql pdo mysqli zip gd bcmath pcntl
COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/role.sh /home/tools/role.sh
COPY --from=composer:2.6.6 /usr/bin/composer /home/tools/composer
RUN groupadd --gid $GID panel &&  \
    useradd --uid $UID --gid panel --shell /bin/bash --create-home panel && \
    chgrp panel -R /home/ && \
    chmod 775 -R /home && \
    chmod +x /home/tools/role.sh

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
USER $UID
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
WORKDIR /home/app
