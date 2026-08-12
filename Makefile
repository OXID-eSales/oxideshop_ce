.PHONY: up down restart setup db-reset db-seed test-db-setup test test-codeception phpcs php mysql

COMPOSE = docker compose
# www-data is remapped (see docker/php/Dockerfile) to the host user's
# uid/gid, so it owns the bind-mounted repo the same as Apache does -
# running as it here keeps composer/oe-console/phpunit output owned by the
# same user as the webserver, avoiding permission-denied errors on shared
# paths like source/log/oxideshop.log or var/configuration.
EXEC_PHP = $(COMPOSE) exec -u www-data php
# Tests run against the separate `example_test` database (.env.test,
# docker/mysql/init-test-db.sql, docker/with-test-env.sh) - never the dev
# `example` database.
EXEC_PHP_TEST = $(COMPOSE) exec -u www-data php bash docker/with-test-env.sh
EXEC_MYSQL = $(COMPOSE) exec mysql

up:
	$(COMPOSE) up -d --build

down:
	$(COMPOSE) down

restart: down up

setup: up
	$(EXEC_PHP) bash docker/setup.sh

# oe:setup:shop refuses to run against a database that already has tables
# (even just an empty schema from a prior migrate), so the dev database has
# to be dropped raw, not just reset/migrated, before shop config can be
# reinstalled.
db-reset:
	$(EXEC_MYSQL) mysql -uroot -proot -e "DROP DATABASE IF EXISTS example; CREATE DATABASE example;"
	$(EXEC_PHP) bin/oe-console oe:setup:shop --language=en
	$(EXEC_PHP) bin/oe-console oe:theme:activate apex
	$(EXEC_PHP) bin/oe-console oe:admin:create-user admin@example.com admin123
	$(EXEC_PHP) bin/oe-console oe:module:install-assets
	$(EXEC_PHP) bin/oe-console oe:cache:clear

db-seed:
	$(EXEC_PHP) bin/oe-console oe:setup:demodata

test-db-setup: up
	$(EXEC_PHP) bash docker/setup-test-db.sh

test: test-db-setup
	$(EXEC_PHP_TEST) vendor/bin/phpunit

test-codeception: test-db-setup
	$(EXEC_PHP_TEST) vendor/bin/codecept run

phpcs:
	$(EXEC_PHP) vendor/bin/phpcs

php:
	$(COMPOSE) exec -u www-data php bash

php-root:
	$(COMPOSE) exec php bash

mysql:
	$(EXEC_MYSQL) bash
