FROM node:21-alpine3.17 as builder
ENV NODE_OPTIONS=--openssl-legacy-provider
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY ./panel/package.json ./panel/yarn.lock ./
RUN ["yarn", "--frozen-lockfile"]
COPY ./panel .
RUN ["yarn", "build:production"]

FROM nginx:1.25.3-alpine3.18
ARG UID
ARG GID
ENV TZ=Europe/Moscow
WORKDIR /home/app
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/configs/default.conf /etc/nginx/conf.d/default.conf
COPY --from=builder /home/app/public /home/app/public
# RUN addgroup -g $GID panel && \
# 	adduser -u $UID -G panel -s /bin/sh -D panel && \
#     chown -R $UID:$GID /var/www/panel && \
#     chmod -R 755 /var/www/panel && \
#     ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
#     echo $TZ > /etc/timezone && \
#     rm -rf /etc/apk/cache
