#!/usr/bin/make

SHELL = /bin/bash

CURRENT_RUN_USER_ID := $(shell id -u)
CURRENT_RUN_USER_NAME := "${USER}"

docker-build:
	docker build -t project-lvl2-php-cli:latest --build-arg "RUN_USER_ID=${CURRENT_RUN_USER_ID}" --build-arg "RUN_USER_NAME=${CURRENT_RUN_USER_NAME}" -f ./Dockerfile .

docker-prepare:
	make docker-build
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` project-lvl2-php-cli:latest make install

docker-run:
	make docker-build
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` project-lvl2-php-cli:latest bash

install:
	composer install

validate:
	composer validate

console:
	composer exec --verbose psysh

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
	composer exec --verbose phpstan -- --level=8 analyse src tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml