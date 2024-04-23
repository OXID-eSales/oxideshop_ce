#!/bin/bash
vendor/bin/phpunit \
    -c phpunit.xml \
    --bootstrap tests/bootstrap.php \
    --coverage-clover=tests/Reports/coverage_phpunit_unit.xml \
    --log-junit tests/Reports/phpunit-unit.xml \
    tests/Unit 2>&1 \
| tee tests/Output/unit_tests.txt
