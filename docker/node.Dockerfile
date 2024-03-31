FROM node:21-alpine3.17
ARG UID
ARG GID
ENV NODE_OPTIONS=--openssl-legacy-provider
ENV TZ=Europe/Moscow
WORKDIR /home/app
RUN apk add shadow && \
    usermod -u $UID node && \
    groupmod -g $GID node && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    rm -rf /etc/apk/cache
USER $UID
ENTRYPOINT ["yarn"]
