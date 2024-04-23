#!/bin/bash
vendor/bin/codecept build \
    -c tests/codeception.yml
SELENIUM_SERVER_HOST=selenium \
BROWSER_NAME=chrome \
DB_NAME=setup_test \
DB_USERNAME=root \
DB_PASSWORD=root \
DB_HOST=mysql \
DB_PORT=3306 \
SHOP_URL=http://localhost.local/ \
SHOP_SOURCE_PATH=/var/www/vendor/oxid-esales/oxideshop-ce/source/ \
THEME_ID=apex \
SHOP_ROOT_PATH=/var/www \
vendor/bin/codecept run acceptanceSetup \
    -c tests/codeception.yml \
    --ext DotReporter 2>&1 \
| tee tests/Output/codeception_ShopSetup.txt
