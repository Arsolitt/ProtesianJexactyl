################ Docker commands ################

# ребилд без глобальных изменений
build:
	docker-compose -f ./.docker/docker-compose.yml build
# создание образа с нуля
rebuild:
	docker-compose -f ./.docker/docker-compose.yml build --no-cache
# создать контейнеры
up:
	docker-compose -f ./.docker/docker-compose.yml up -d
# выключить контейнеры
down:
	docker-compose -f ./.docker/docker-compose.yml down
restart:
	docker-compose -f ./.docker/docker-compose.yml restart
