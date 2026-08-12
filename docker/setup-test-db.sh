#!/usr/bin/env bash
# Ensures the *test* database (example_test, see .env.test / docker/mysql/init-test-db.sql)
# has the shop schema installed, without touching the dev database (example).
# Idempotent: skips oe:setup:shop if the test DB already has tables.
set -euo pipefail
cd /var/www

set -a
# shellcheck disable=SC1091
source .env.test
set +a

TABLE_COUNT=$(mysql -h mysql -uroot -proot --skip-ssl -N -B \
    -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='example_test';")

if [ "$TABLE_COUNT" -eq 0 ]; then
    bin/oe-console oe:setup:shop --language=en
else
    bin/oe-console oe:database:migrate
fi

echo "Test database ready (database example_test)."
