FROM nginx:1.25.3-alpine3.18
# ARG UID
# ARG GID
ENV UID=1000
ENV GID=1000
ENV TZ=Europe/Moscow
RUN addgroup -g $GID panel && \
	adduser -u $UID -G panel -s /bin/sh -D panel && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    rm -rf /etc/apk/cache
