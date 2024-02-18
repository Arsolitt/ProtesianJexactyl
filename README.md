<h1 align="center">Форк панели управления [Pterodactyl Software](https://pterodactyl.io)</h1>

## Установка

Гайда по продакшен установке не будет, я не причесал докер файлы и вообще не все фичи реализованы

Для развёртвания окружения разработки нужно: `docker`, `docker-compose`, `make`

```shell
git clone git@github.com:Arsolitt/ProtesianJexactyl.git .
```

Прописать конфиг в .env файл

```shell
cp .env.example .env
```

Чтобы включить https нужно:

- Сгенерировать сертификаты
- Положить сертификаты в `docker/nginx/fullchain.pem` и `docker/nginx/privkey.pem`
- Убрать комментарии на 52 и 53 строке в `docker/nginx/default.conf`

Создать .env для лары

```shell
cd panel && cp .env.example .env
```

Для запуска artisan / composer / yarn использовать helper-контейнеры

```shell
docker-compose run --rm artisan/composer/yarn <команда>
```

Сборка докер-образа

```shell
make build
```

Установка зависимостей

```shell
docker-compose run --rm composer install --no-dev --optimize-autoloader
```

Генерация ключа

```shell
docker-compose run --rm artisan key:generate --force
```

Настройка .env ларавела

```shell
docker-compose run --rm artisan p:environment:setup
```

```shell
docker-compose run --rm artisan p:environment:database
```

Сборка фронта

```shell
docker-compose run --rm yarn build:production
```

Или отслеживание изменений

```shell
docker-compose run --rm yarn watch
```

Запуск композа с проектом

```shell
make up
```

Создание пользователя

```shell
docker-compose run --rm artisan p:user:make
```

## Превью

<strong>Дизайн на переработке под цветовую палитру проекта</strong>
![image](https://user-images.githubusercontent.com/72230943/201116518-af5e3291-74f7-433a-b035-6d80e8c7e8f8.png)
![image](https://user-images.githubusercontent.com/72230943/201116580-ae864e7c-aac7-4766-ab9c-c6cb97d0b015.png)
![image](https://user-images.githubusercontent.com/72230943/201116688-b53d721e-c30f-424e-8a53-025f313ec98f.png)
![image](https://user-images.githubusercontent.com/72230943/201116840-92c00c15-5717-4121-83cd-69397f9bacba.png)
![image](https://user-images.githubusercontent.com/72230943/201116914-8b1c8867-c462-4b25-ae47-803b2e4ea39c.png)
![image](https://user-images.githubusercontent.com/72230943/201116959-a626e6fc-18a9-4c06-869e-2f13b37b8457.png)
![image](https://user-images.githubusercontent.com/72230943/201117028-3db8aa2e-b14b-4679-9f2c-c5afb208767c.png)

## Licensing

Некоторые Javascript и CSS, используемые в панели, лицензированы по лицензии `MIT` или `Apache 2.0`

*Этот репозиторий никак не связан с  [Pterodactyl Software](https://pterodactyl.io), кроме как тем, что я разрабатываю
на их основе
