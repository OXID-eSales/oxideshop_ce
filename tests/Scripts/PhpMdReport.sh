#!/bin/bash
vendor/bin/phpmd \
    source json tests/PhpMd/standard.xml \
    --ignore-errors-on-exit \
    --ignore-violations-on-exit \
    --reportfile tests/Reports/phpmd.report.json
