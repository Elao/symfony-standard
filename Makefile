.SILENT:
.PHONY: build test

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

###############
# Environment #
###############

## Setup environment & Install & Build application
setup:
	vagrant up --no-provision
	vagrant provision
	vagrant ssh -- "cd /srv/app && make install build"

## Update environment
update: export ANSIBLE_TAGS = manala.update
update:
	vagrant provision

## Update ansible
update-ansible: export ANSIBLE_TAGS = manala.update
update-ansible:
	vagrant provision --provision-with ansible

## Provision environment
provision: export ANSIBLE_EXTRA_VARS = {"manala":{"update":false}}
provision:
	vagrant provision --provision-with app

## Provision nginx
provision-nginx: export ANSIBLE_TAGS = manala_nginx
provision-nginx: provision

## Provision php
provision-php: export ANSIBLE_TAGS = manala_php
provision-php: provision

###########
# Install #
###########

## Install application
install:
	# Composer
	composer install --verbose
	# Db
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update --force
	# Db - Test
	bin/console doctrine:database:create --if-not-exists --env=test
	bin/console doctrine:schema:update --force --env=test
	# Db - Fixtures
	#bin/console doctrine:fixtures:load --no-interaction
	# Db - Fixtures - Test
	#bin/console doctrine:fixtures:load --no-interaction --env=test

install@test: export SYMFONY_ENV = test
install@test:
	# Composer
	composer install --verbose --no-progress --no-interaction
	# Db
	bin/console doctrine:database:drop --force --if-exists
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:schema:update --force
	# Db - Fixtures
	#bin/console doctrine:fixtures:load --no-interaction

install@demo: export SYMFONY_ENV = prod
install@demo:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader
	# Symfony cache
	bin/console cache:warmup --no-debug

install@prod: export SYMFONY_ENV = prod
install@prod:
	# Composer
	composer install --verbose --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-dev
	# Symfony cache
	bin/console cache:warmup --no-debug

#########
# Build #
#########

## Build application
build:

build@demo: export SYMFONY_ENV = prod
build@demo:

build@prod: export SYMFONY_ENV = prod
build@prod:

############
# Security #
############

## Run security checks
security:
	security-checker security:check

security@test: export SYMFONY_ENV = test
security@test: security

########
# Lint #
########

## Run lint tools
lint:
	php-cs-fixer fix --config-file=.php_cs --dry-run --diff

lint@test: export SYMFONY_ENV = test
lint@test: lint

########
# Test #
########

## Run tests
test: export SYMFONY_ENV = test
test:
	# PHPUnit
	vendor/bin/phpunit
	# Behat
	bin/console cache:clear && vendor/bin/behat

test@test: export SYMFONY_ENV = test
test@test:
	# PHPUnit
	rm -Rf build/phpunit && mkdir -p build/phpunit
	stty cols 80 && vendor/bin/phpunit --log-junit build/phpunit/junit.xml --coverage-clover build/phpunit/clover.xml --coverage-html build/phpunit/coverage
	# Behat
	rm -Rf build/behat && mkdir -p build/behat
	bin/console cache:clear && vendor/bin/behat --format=junit --out=build/behat --no-interaction

##########
# Deploy #
##########

## Deploy application (demo)
deploy@demo:
	ansible-playbook ansible/deploy.yml --inventory-file=ansible/hosts --limit=deploy_demo

## Deploy application (prod)
deploy@prod:
	ansible-playbook ansible/deploy.yml --inventory-file=ansible/hosts --limit=deploy_prod

##########
# Custom #
##########
