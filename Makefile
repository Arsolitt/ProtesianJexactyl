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
        docker exec $(shell docker ps -f name=jexactyl_database --quiet) mariadb-dump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > panel/storage/files/backup.sql && echo "Success!"

#####################################################
################ PRODUCTION COMMANDS ################
#####################################################

prod_build:
	docker-compose build

# START SERVICES
prod_up:
	docker-compose up -d

# STOP SERVICES
prod_down:
	docker-compose down

# RESTART SERVICES
prod_restart:
	docker-compose restart

# BUILD FRONTEND FILES
prod_build_front:
	docker-compose run --rm yarn --fronzen-lockfile
	docker-compose run --rm yarn build:production

# SETUP DEPS AND MIGRATIONS
prod_migrations:
	docker-compose run --rm composer install --no-dev --optimize-autoloader
	docker-compose run --rm artisan migrate --force
