.PHONY: up down build install test

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

install:
	docker-compose exec php composer install

test:
	docker-compose exec php bin/phpunit

shell:
	docker-compose exec php bash