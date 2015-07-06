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

## Clean
clean:
	@rm -rf var/build/*
	@mkdir -p var/build/logs var/build/phpunit
	@composer run-script post-install-cmd --no-interaction

## Prepare vendor
prepare-vendor:
	@composer install -n
	@npm install

## Prepare
prepare:
	@php bin/console doctrine:database:create --if-not-exists
	@php bin/console doctrine:schema:update --force

## Build
build:
	@gulp

## Install
install: prepare-vendor prepare build

## Prepare test
prepare-test: clean prepare-vendor prepare-test build
	@php bin/console doctrine:database:create --if-not-exists
	@php bin/console doctrine:schema:drop --force --env=test
	@php bin/console doctrine:schema:create --env=test

## Coverage
coverage:
	@bin/phpunit -c app --colors --coverage-html var/build/phpunit --coverage-clover var/build/logs/clover.xml

## Test
test:
	@bin/phpunit -c app --colors --log-junit var/build/logs/junit.xml
	@bin/behat -f progress
