#!/bin/bash
set -e
SUITE="Acceptance"
if [ ! -d "tests/Codeception/${SUITE}" ]; then
  SUITE="acceptance"
  if [ ! -d "tests/Codeception/${SUITE}" ]; then
    echo -e "\033[0;31mCould not find suite Acceptance or acceptance in tests/Codeception\033[0m"
    exit 1
  fi
fi

CODECEPT="vendor/bin/codecept"
if [ ! -f "${CODECEPT}" ]; then
    CODECEPT="/var/www/${CODECEPT}"
    if [ ! -f "${CODECEPT}" ]; then
        echo -e "\033[0;31mCould not find codecept in vendor/bin or /var/www/vendor/bin\033[0m"
        exit 1
    fi
fi
# wait for selenium host
I=60
until  [ $I -le 0 ]; do
    curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status" |grep '"ready": true' && break
    echo "."
    sleep 1
    ((I--))
done
set -e
curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status"

"${CODECEPT}" build \
    -c tests/codeception.yml
RESULT=$?
echo "Codecept build exited with error code ${RESULT}"
"${CODECEPT}" run ${SUITE} \
    -c tests/codeception.yml \
    --ext DotReporter 2>&1 \
| tee "tests/Output/codeception_${SUITE}.txt"
RESULT=$?
echo "Codecept run exited with error code ${RESULT}"
[[ ! -d tests/Output ]] && mkdir tests/Output
find tests/Codeception/_output -type f -exec cp \{\} tests/Output/ \;
if [ ! -s "tests/Output/codeception_${SUITE}.txt" ]; then
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
        if grep -q -E "${LINE}" "tests/Output/codeception_${SUITE}.txt"; then
            echo -e "\033[0;31m codecept ${SUITE} failed matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "tests/Output/codeception_${SUITE}.txt"
            RESULT=1
        else
            echo -e "\033[0;32m codeception passed matching pattern ${LINE}"
        fi
    fi
done <failure_pattern.tmp
exit ${RESULT}
