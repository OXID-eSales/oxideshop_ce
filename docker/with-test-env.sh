#!/usr/bin/env bash
# Runs a command with the test-database overrides from .env.test exported as
# real process env vars. Symfony Dotenv never overrides an already-set env
# var, so this makes OXID_DB_URL/OXID_BUILD_DIRECTORY win over .env without
# touching OXID_ENV itself - some integration tests (EnvLoaderTest,
# BasicContextTest, ContainerBuilderTest...) exercise OXID_ENV-driven
# env-switching directly and break if it's forced to a fixed value here.
set -euo pipefail
cd /var/www

set -a
# shellcheck disable=SC1091
source .env.test
set +a

exec "$@"
