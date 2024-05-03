FROM nginx:1.25.3-alpine3.18
ARG UID
ARG GID
ENV TZ=Europe/Moscow
WORKDIR /var/www/panel
RUN addgroup -g $GID panel && \
	adduser -u $UID -G panel -s /bin/sh -D panel && \
    chown -R $UID:$GID /var/www/panel && \
    chmod -R 755 /var/www/panel && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    rm -rf /etc/apk/cache
