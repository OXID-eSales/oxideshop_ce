#!/bin/bash
set -e
vendor/bin/phpstan \
    -ctests/PhpStan/phpstan.neon \
    analyse source/ \
    --error-format=json \
    >"tests/Reports/phpstan.report.json"
