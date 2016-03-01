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
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

#########
# Setup #
#########

## Setup environment & Install application
setup: provision
	vagrant ssh -c 'cd /srv/app && make install'

setup@test: provision@test install@test

#############
# Provision #
#############

## Provision environment
provision: provision-vagrant

provision@test: provision-ansible@test

provision-services@test: provision-services-ansible@test

provision-vagrant:
	ansible-galaxy install -r ansible/roles/requirements.yml -p ansible/roles -f
	vagrant up --no-provision
	vagrant provision

provision-ansible@test:
	ansible-galaxy install -r ansible/roles/requirements.yml -p ansible/roles -f
	ansible-playbook -i ansible/hosts -l env_test,app -s -e "_user=${_ANSIBLE_USER}" --force-handlers ansible/setup.yml

provision-services-ansible@test:
	ansible-playbook -i ansible/hosts -l env_test,app -s -e "_user=${_ANSIBLE_USER}" --force-handlers --tags=elao_services ansible/setup.yml

###########
# Install #
###########

## Install application
install: install-app install-db install-db@test install-db-fixtures install-db-fixtures@test install-dep build

install@test: install-app@test install-db@test install-db-fixtures@test install-dep build@prod

install@prod: install-dep build@prod

install-app:
	composer --no-progress --no-interaction install

install-app@test:
	SYMFONY_ENV=test composer --no-progress --no-interaction install

install-db:
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update --force

install-db@test:
	bin/console doctrine:database:drop --force --if-exists --env=test
	bin/console doctrine:database:create --env=test
	bin/console doctrine:schema:create --env=test

install-db-fixtures:
	#bin/console doctrine:fixtures:load -n

install-db-fixtures@test:
	#bin/console doctrine:fixtures:load -n --env=test

install-dep:
	npm --no-spin install

#########
# Build #
#########

## Build application
build: build-assets

build@prod: build-assets@prod

build-assets:

build-assets@prod:

########
# Test #
########

## Run tests
test: test-phpunit test-behat

test@test: test-phpunit@test test-behat@test

test-phpunit:
	vendor/bin/phpunit

test-phpunit@test:
	mkdir -b build
	rm -rf build/junit.xml build/clover.xml build/coverage
	stty cols 80; vendor/bin/phpunit --log-junit build/junit.xml --coverage-clover build/clover.xml --coverage-html build/coverage

test-behat:
	vendor/bin/behat

test-behat@test:
	vendor/bin/behat --format=progress --no-interaction

##########
# Deploy #
##########

## Deploy application (demo)
deploy@demo: deploy-capifony@demo

## Deploy application (prod)
deploy@prod: deploy-capifony@prod

deploy-capifony@demo:
	cap demo deploy

deploy-capifony@prod:
	cap prod deploy

##########
# Custom #
##########
