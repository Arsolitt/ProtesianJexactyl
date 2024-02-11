FROM nginx:1.25.3-alpine3.18

ARG UID
ARG GID

RUN apk add zsh git

RUN addgroup -g $GID panel \
	&& adduser -u $UID -G panel -s /bin/sh -D panel

COPY docker/nginx/nginx.conf /etc/nginx/
COPY docker/nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/panel

RUN chown -R $UID:$GID /var/www/panel \
    && chmod -R 755 /var/www/panel

RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
