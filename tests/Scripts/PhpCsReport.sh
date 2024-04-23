#!/bin/bash
phpcs \
    --standard=tests/phpcs.xml \
    --report=json \
    --report-file=tests/Reports/phpcs.report.json \
||true
# As the first one does not produce legible output, this gives us something to see in the log
phpcs \
    --standard=tests/phpcs.xml \
    --report=full
