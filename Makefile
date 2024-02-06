include .env
################ Docker commands ################

# ребилд без глобальных изменений
build:
	docker-compose build
# создание образа с нуля
rebuild:
	docker-compose build --no-cache
# создать контейнер
up:
	docker-compose up -d
# выключить контейнер
down:
	docker-compose down
# зайти в терминал контейнера
shell_php:
	docker exec -it ${PROJECT_NAME}_php /usr/bin/zsh

shell_db:
	docker exec -it ${PROJECT_NAME}_database /usr/bin/bash

shell_node:
	docker exec -it ${PROJECT_NAME}_node /usr/bin/zsh

shell_nginx:
	docker exec -it ${PROJECT_NAME}_nginx /bin/zsh
