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

#####################################################
################ PRODUCTION COMMANDS ################
#####################################################

production_build:
	docker-compose -f docker-compose.prod.yml build

# START SERVICES
production_up:
	docker-compose -f docker-compose.prod.yml up -d

# STOP SERVICES
production_down:
	docker-compose -f docker-compose.prod.yml down

# RESTART SERVICES
production_restart:
	docker-compose -f docker-compose.prod.yml restart

# SETUP
production_setup:
	make production_down
	make production_build

	make production_up
	docker-compose -f docker-compose.prod.yml run --rm composer install --no-dev --optimize-autoloader
	docker-compose run --rm artisan migrate --force
	docker-compose -f docker-compose.prod.yml run --rm yarn --fronzen-lockfile
	docker-compose -f docker-compose.prod.yml run --rm yarn build:production

# IMPORT USERS.JSON
production_import:
	docker-compose run --rm artisan app:import-users-from-json
