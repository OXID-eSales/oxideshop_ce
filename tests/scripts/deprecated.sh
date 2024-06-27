#!/bin/bash
set -e
export XDEBUG_MODE=coverage
SUITE="AllTestsUnit"
RUNTEST="vendor/bin/runtests"
if [ ! -f "${RUNTEST}" ]; then
    RUNTEST="/var/www/${RUNTEST}"
    if [ ! -f "${RUNTEST}" ]; then
        echo -e "\033[0;31mCould not find runtests in vendor/bin or /var/www/vendor/bin\033[0m"
        exit 1
    fi
fi
cp vendor/oxid-esales/testing-library/test_config.yml.dist test_config.yml
"${RUNTEST}" \
    --coverage-clover=tests/Reports/coverage_deprecated_tests.xml \
    "${SUITE}" 2>&1 \
| tee "tests/Output/deprecated_tests.txt"
RESULT=$?
echo "runtest exited with error code ${RESULT}"

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
if [[ ! -s "tests/Reports/coverage_deprecated_tests.xml" ]]; then
    echo -e "\033[0;31m coverage report tests/Reports/coverage_deprecated_tests.xml is empty\033[0m"
    RESULT=1
fi
exit ${RESULT}
