install: prepare-vendor prepare

test: clean prepare-vendor prepare-test build

clean:
	@rm -rf var/cache/* var/logs/* var/build/*
	@mkdir -p var/build/logs
	@mkdir -p var/build/phpunit
	-@rm app/config/parameters.yml
	cp app/config/parameters.yml.dist app/config/parameters.yml

prepare-vendor:
	@composer install -n
	@npm install
	@bower install
	@gulp install

prepare:
	-@php bin/console doctrine:schema:create
	@php bin/console doctrine:schema:update --force

prepare-test:
	@php bin/console doctrine:schema:drop --force --env=test
	@php bin/console doctrine:schema:create

build:
	@bin/phpunit -c app --colors --coverage-html var/build/phpunit --coverage-clover var/build/logs/clover.xml --log-junit var/build/logs/junit.xml
	@bin/behat -f progress
