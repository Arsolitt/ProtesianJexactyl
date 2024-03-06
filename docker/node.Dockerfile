FROM node:21-bookworm

ARG UID
ARG GID
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN apt-get update && apt-get install -y nano zsh
RUN usermod -u $UID node \
  && groupmod -g $GID node

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
USER $UID
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
WORKDIR /home/app
ENTRYPOINT ["yarn"]
