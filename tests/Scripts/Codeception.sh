#!/bin/bash
vendor/bin/codecept build \
    -c tests/codeception.yml
vendor/bin/codecept run acceptanceSetup \
    -c tests/codeception.yml \
    --ext DotReporter 2>&1 \
| tee tests/Output/codeception_ShopSetup.txt
