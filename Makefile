include .env

################ Docker commands ################

# ребилд без глобальных изменений
build:
	docker-compose -f docker-compose.dev.yml build
# создание образа с нуля
rebuild:
	docker-compose -f docker-compose.dev.yml build --no-cache
# создать контейнер
up:
	docker-compose -f docker-compose.dev.yml up -d
# выключить контейнер
down:
	docker-compose -f docker-compose.dev.yml down

backup:
	docker exec ${PROJECT_NAME}_database mariadb-dump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > panel/storage/files/backup.sql && echo "Success!"

restore:
	docker exec ${PROJECT_NAME}_database mariadb -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < panel/storage/files/backup.sql && echo "Success!"

#####################################################
################ PRODUCTION COMMANDS ################
#####################################################

prod_build:
	docker-compose -f docker-compose.prod.yml build

# START SERVICES
prod_up:
	docker-compose -f docker-compose.prod.yml up -d

# STOP SERVICES
prod_down:
	docker-compose -f docker-compose.prod.yml down

# RESTART SERVICES
prod_restart:
	docker-compose -f docker-compose.prod.yml restart

# BUILD FRONTEND FILES
prod_build_front:
	docker-compose -f docker-compose.prod.yml run --rm yarn --fronzen-lockfile
	docker-compose -f docker-compose.prod.yml run --rm yarn build:production

# SETUP DEPS AND MIGRATIONS
prod_migrations:
	docker-compose -f docker-compose.prod.yml run --rm composer install --no-dev --optimize-autoloader
	docker-compose -f docker-compose.prod.yml run --rm artisan migrate --force
# SETUP
prod_setup:
	make prod_down
	make prod_build
	make prod_up
	make prod_migrations
	make prod_build_front

prod_update:
	make prod_down
	make prod_build
	make prod_up
	make prod_migrations
