#!/bin/bash
set -e
vendor/bin/codecept build \
    -c tests/codeception.yml
RESULT=$?
echo "Codecept build exited with error code ${RESULT}"
vendor/bin/codecept run acceptance \
    -c tests/codeception.yml \
    --ext DotReporter 2>&1 \
| tee tests/Output/codeception_Acceptance.txt
RESULT=$?
echo "Codecept run exited with error code ${RESULT}"
[[ ! -d tests/Output ]] && mkdir tests/Output
cp tests/Codeception/_output/* tests/Output
if [ ! -s "tests/Output/codeception_Acceptance.txt" ]; then
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
        if grep -q -E "${LINE}" "tests/Output/codeception_Acceptance.txt"; then
            echo -e "\033[0;31m codecept ${SUITE} failed matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "tests/Output/codeception_Acceptance.txt"
            RESULT=1
        else
            echo -e "\033[0;32m codeception passed matching pattern ${LINE}"
        fi
    fi
done <failure_pattern.tmp
exit ${RESULT}
