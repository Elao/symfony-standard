.SILENT:
.PHONY: help

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Setup environment & Install application
setup:
	ansible-galaxy install -r ansible/roles.yml -p ansible/roles -f
	vagrant up --provision
	vagrant ssh -c 'cd /srv/app/symfony && make install'

## Install application
install: prepare-vendor prepare-db prepare-db-test build

install-test: clean prepare-vendor prepare-db-test build

prepare-vendor:
	composer install -n
	npm install

prepare-db:
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update --force
	#php bin/console doctrine:fixtures:load -n

prepare-db-test:
	php bin/console doctrine:database:create --if-not-exists --env=test
	php bin/console doctrine:schema:drop --force --env=test
	php bin/console doctrine:schema:create --env=test

build:
	gulp

clean:
	rm -rf var/build/*
	rm -rf var/cache/*
	mkdir -p var/build/logs var/build/phpunit
	composer run-script post-install-cmd --no-interaction

## Run tests
test:
	@bin/phpunit -c app --colors --log-junit var/build/logs/junit.xml
	@bin/behat -f progress

## Generate coverage report
coverage:
	@bin/phpunit -c app --colors --coverage-html var/build/phpunit --coverage-clover var/build/logs/clover.xml

## Deploy app to demo
deploy-demo:
	cap demo deploy

## Deploy app to production
deploy-prod:
	cap prod deploy
