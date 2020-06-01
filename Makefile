.PHONY: run
run:
	@cd docker && docker-compose run -u dev workplace $(filter-out $@,$(MAKECMDGOALS))

.PHONY: dev
dev:
	@cd docker && docker-compose exec -u dev workplace bash

##################
# Docker compose
##################

.PHONY: dc_start
dc_start:
	@cd docker && docker-compose start

.PHONY: dc_stop
dc_stop:
	@cd docker && docker-compose stop

.PHONY: dc_up
dc_up:
	@cd docker && docker-compose up -d

.PHONY: dc_ps
dc_ps:
	@cd docker && docker-compose ps

.PHONY: dc_down
dc_down:
	@cd docker && docker-compose down -v --rmi=all --remove-orphans

##################
# Setup
##################

.PHONY: setup_dev
setup_dev:
	cd docker && docker-compose run -u dev workplace composer install
	cd docker && docker-compose run -u dev workplace php bin/console doctrine:migrations:migrate --no-interaction
	cd docker && docker-compose run -u dev workplace php bin/console cache:clear

##################
# CI (workplace container)
##################

.PHONY: analyze
analyze: deptrac cs_check phpmnd phpcpd phpstan security_check schema_validate phpunit

.PHONY: cs_check
cs_check:
	php-cs-fixer fix --config=.php_cs -v --allow-risky=yes --dry-run --diff --stop-on-violation

.PHONY: cs_fix
cs_fix:
	php-cs-fixer fix --config=.php_cs -v --allow-risky=yes --diff

.PHONY: schema_validate
schema_validate:
	php bin/console doctrine:cache:clear-metadata
	php bin/console doctrine:schema:validate

.PHONY: phpmnd
phpmnd:
	/home/dev/.composer/vendor/bin/phpmnd src -v --progress --extensions=all --non-zero-exit-on-violation

.PHONY: phpcpd
phpcpd:
	./vendor/bin/phpcpd src --exclude=Entity

.PHONY: security_check
security_check:
	php bin/console security:check

.PHONY: phpstan
phpstan:
	php ./vendor/bin/phpstan analyse src -c phpstan.neon

.PHONY: deptrac
deptrac:
	php ./vendor/bin/deptrac analyze depfile.yaml

.PHONY: phpunit
phpunit:
	php ./vendor/bin/phpunit

