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

backup:
	docker exec -ti ${PROJECT_NAME}_database mariadb-dump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > panel/storage/files/backup.sql && echo "Success!"
