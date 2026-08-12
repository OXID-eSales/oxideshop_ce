OXID eShop
==========

This repository contains the sources of OXID eShop Community Edition Core Component.

### About OXID eShop:

OXID eShop is a flexible e-commerce software with a wide range of functionalities. 
Thanks to its modular, modern and state-of-the-art architecture, it can be modified, expanded 
and customized to individual requirements with the greatest of ease. 

OXID eShop is just e-commerce software for agencies with deadlines :-)

### Installation

#### Compilation installation

For full installation instructions, please check the [OXID eShop compilation installation manual](https://docs.oxid-esales.com/developer/en/latest/getting_started/installation/eshop_installation.html).

#### Installation for Contributors

Information how to install development version and make a pull request can be found in [CONTRIBUTING.md](CONTRIBUTING.md) file.

#### Docker Compose dev environment

A self-contained Docker Compose setup is included for local development, no host PHP/MySQL required.

```
make setup   # builds images, starts containers, installs composer deps, runs DB migrations/demodata
```

Then open http://localhost/ in a browser (admin backend at http://localhost/admin/). Mapped to host port 80, not e.g. 8080, so the shop's own self-referential HTTP calls (module API routes, some integration tests) resolve the same URL from inside the container as the browser uses from outside - see `docker-compose.yml` for details. If port 80 is already taken on your host, change the `php` service's port mapping and update `OXID_SHOP_BASE_URL` in `.env` to match, then `make db-reset`.

Other available targets:

* `make up` / `make down` / `make restart` - start/stop/restart all containers
* `make db-reset` - drop and fully recreate the dev database (`example`), including shop setup, theme activation, admin user and module assets
* `make db-seed` - re-run demo data installation against the current schema
* `make test` - run PHPUnit inside the `php` container, against the isolated test database (see below)
* `make test-codeception` - run Codeception suites inside the `php` container, against the isolated test database
* `make phpcs` - run PHP_CodeSniffer inside the `php` container
* `make php` / `make mysql` - open a shell in the `php` or `mysql` container (`make php-root` for a root shell, e.g. for `apt-get`)

Services: `php` (Apache + PHP 8.4, port 80), `mysql` (MySQL 8.0, port 3306), `mailpit` (catches outgoing shop emails, UI at http://localhost:8025/), `adminer` (DB UI at http://localhost:8081/).

`docker/setup.sh` checks `bin/oe-console list` before running each setup step and skips anything not registered by the currently installed packages - if a command is skipped, check the actual name with `make php` then `bin/oe-console list`.

This repo only requires the demo data *installer mechanism* (`oxid-esales/oxideshop-demodata-installer`), not the actual demo data content. `oe:setup:demodata` will report a missing `demodata.sql` file until you add the content package too, e.g. `make php` then `composer require --dev oxid-esales/oxideshop-demodata-ce`, followed by `make db-seed`.

This `oxideshop-ce` package is core-only and ships no theme, so `docker/setup.sh` installs and activates `oxid-esales/apex-theme` (storefront) and `oxid-esales/twig-admin-theme` (admin backend), both pinned to `dev-b-8.0.x` to match this checkout's core packages.

#### Test database isolation

`make test`/`make test-codeception` never touch the dev database. They run through `docker/with-test-env.sh`, which exports the overrides from `.env.test` (`OXID_DB_URL` pointing at a separate `example_test` database, `OXID_BUILD_DIRECTORY` pointing at a separate `var/cache_test/`) as real process env vars before invoking phpunit/codeception - Symfony Dotenv never overrides an already-set env var, so these win over `.env` without needing `OXID_ENV` to change (forcing `OXID_ENV` broke tests that specifically exercise OXID's own env-switching logic). `example_test` is created automatically by `docker/mysql/init-test-db.sql` on first `mysql` container start; `make test-db-setup` (a dependency of `test`/`test-codeception`) installs its schema on first use and is a no-op afterwards.

### IDE code completion

You can easily enable code completion in your IDE by installing [this script](https://github.com/OXID-eSales/eshop-ide-helper) and generating it as described.

### Useful links

* Vendor home page - https://www.oxid-esales.com
* Bug tracker - https://bugs.oxid-esales.com
* SDK - https://github.com/OXID-eSales/docker-eshop-sdk
