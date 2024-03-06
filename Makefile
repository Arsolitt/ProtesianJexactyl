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

restore:
	docker exec -ti ${PROJECT_NAME}_database mariadb -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < panel/storage/files/backup.sql && echo "Success!"

################ PRODUCTION COMMANDS ################

production_build:
	docker-compose -f docker-compose.storage.yml build
	docker-compose -f docker-compose.prod.yml build


# START SERVICES
production_up_storage:
	docker-compose -f docker-compose.storage.yml up -d
production_up_app:
	docker-compose -f docker-compose.prod.yml up -d
production_up_all:
	make production_up_storage
	make production_up_app

# STOP SERVICES
production_down_storage:
	docker-compose -f docker-compose.storage.yml down
production_down_app:
	docker-compose -f docker-compose.prod.yml down
production_down_all:
	make production_down_app
	make production_down_storage

# RESTART SERVICES
production_restart_storage:
	docker-compose -f docker-compose.storage.yml down
	docker-compose -f docker-compose.storage.yml up -d
production_restart_app:
	docker-compose -f docker-compose.prod.yml down
	docker-compose -f docker-compose.prod.yml up -d
production_restart_all:
	make production_down_app
	make production_down_storage
	make production_up_storage
	make production_up_app



# SETUP
production_setup:
	make production_down_all
	make production_build
	make production_up_storage

	docker-compose -f docker-compose.prod.yml run --rm composer install --no-dev --optimize-autoloader
	docker-compose -f docker-compose.prod.yml run --rm yarn --fronzen-lockfile
	docker-compose -f docker-compose.prod.yml run --rm yarn build:production

	make production_up_app
	docker-compose run --rm artisan migrate --force


# IMPORT USERS.JSON
production_import:
	docker-compose run --rm artisan app:import-users-from-json
