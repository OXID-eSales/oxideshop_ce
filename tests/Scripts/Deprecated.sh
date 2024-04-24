#!/bin/bash
set -e
cp /var/www/vendor/oxid-esales/testing-library/test_config.yml.dist test_config.yml
# sed -e "s|shop_tests_path:.*|shop_tests_path: 'vendor/oxid-esales/tests'|" \
#     -e "s|shop_path:.*|shop_path: 'vendor/oxid-esales/oxideshop-ce/source'|" \
#     -i test_config.yml
vendor/bin/runtests \
    --coverage-clover=tests/Reports/deprecated_tests_coverage.xml \
    AllTestsUnit 2>&1 \
| tee "tests/Output/deprecated_tests.txt"
