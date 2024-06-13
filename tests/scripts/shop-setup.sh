#!/bin/bash
set -e
export SELENIUM_SERVER_HOST=selenium
export BROWSER_NAME=chrome
export DB_NAME=setup_test
export DB_USERNAME=root
export DB_PASSWORD=root
export DB_HOST=mysql
export DB_PORT=3306
export SHOP_URL=http://localhost.local/
export SHOP_SOURCE_PATH=/var/www/vendor/oxid-esales/oxideshop-ce/source/
export THEME_ID=apex
export SHOP_ROOT_PATH=/var/www
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

vendor/bin/codecept build \
    -c tests/codeception.yml
RESULT=$?
echo "codecept build exited with error code ${RESULT}"
vendor/bin/codecept run AcceptanceSetup \
    -c tests/codeception.yml \
    --ext DotReporter 2>&1 \
| tee tests/Output/codeception_ShopSetup.txt
RESULT=$?
echo "codecept run exited with error code ${RESULT}"
if [ ! -s "tests/Output/codeception_ShopSetup.txt" ]; then
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
        if grep -q -E "${LINE}" "tests/Output/codeception_ShopSetup.txt"; then
            echo -e "\033[0;31m runtest failed matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "tests/Output/codeception_ShopSetup.txt"
            RESULT=1
        else
            echo -e "\033[0;32m codeception passed matching pattern ${LINE}"
        fi
    fi
done <failure_pattern.tmp
exit ${RESULT}
