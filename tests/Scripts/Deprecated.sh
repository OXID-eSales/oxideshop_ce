#!/bin/bash
set -e
cp vendor/oxid-esales/testing-library/test_config.yml.dist test_config.yml
# sed -e "s|shop_tests_path:.*|shop_tests_path: 'vendor/oxid-esales/tests'|" \
#     -e "s|shop_path:.*|shop_path: 'vendor/oxid-esales/oxideshop-ce/source'|" \
#     -i test_config.yml
XDEBUG_MODE=coverage vendor/bin/runtests \
    --coverage-clover=tests/Reports/deprecated_tests_coverage.xml \
    AllTestsUnit 2>&1 \
| tee "tests/Output/deprecated_tests.txt"

RESULT=0
if [ ! -s "tests/Output/deprecated_tests.txt" ]; then
    echo -e "\033[0;31mLog file is empty! Seems like no tests have been run!\033[0m"
    RESULT=1
fi
cat >failure_pattern.tmp <<EOF
fail
\\.\\=\\=
Warning
Notice
Deprecated
Fatal
Error
DID NOT FINISH
Test file ".+" not found
Cannot open file
No tests executed
Could not read
Warnings: [1-9][0-9]*
Errors: [1-9][0-9]*
Failed: [1-9][0-9]*
Deprecations: [1-9][0-9]*
Risky: [1-9][0-9]*
EOF
sed -e 's|(.*)\r|$1|' -i failure_pattern.tmp
while read -r LINE ; do
    if [ -n "${LINE}" ]; then
        if grep -q -E "${LINE}" "tests/Output/deprecated_tests.txt"; then
            echo -e "\033[0;31m runtest failed matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "tests/Output/deprecated_tests.txt"
            RESULT=1
        else
            echo -e "\033[0;32m runtest passed matching pattern ${LINE}"
        fi
    fi
done <failure_pattern.tmp
if [[ ! -s "tests/Reports/deprecated_tests_coverage.xml" ]]; then
    echo -e "\033[0;31m coverage report tests/Reports/deprecated_tests_coverage.xml is empty\033[0m"
    RESULT=1
fi
exit ${RESULT}
