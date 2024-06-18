#!/bin/bash
# shellcheck disable=SC2154
# Lower case environment variables are passed from the workflow and used here
# We use a validation loop in init to ensure, they're set
# shellcheck disable=SC2086
# We want install_container_options to count as multiple arguments
set -e

function error() {
    echo -e "\033[0;31m${1}\033[0m"
    exit 1
}

function init() {
    for VAR in install_container_method install_container_options install_container_name \
        install_config_idebug install_is_enterprise; do
        echo -n "Checking, if $VAR is set ..."
        if [ -z ${VAR+x} ]; then
            error "Variable '${VAR}' not set"
        fi
        echo "OK, ${VAR}='${!VAR}'"
    done
    echo -n "Locating oe-console ... "
    cd source || exit 1
    if [ -f 'bin/oe-console' ]; then
        OE_CONSOLE='bin/oe-console'
    else
        if [ -f 'vendor/bin/oe-console' ]; then
        OE_CONSOLE='vendor/bin/oe-console'
        else
            error "Can't find oe-console in bin or vendor/bin!"
        fi
    fi
    echo "OK, using '${OE_CONSOLE}'"
}

init
# Run Install Shop
docker compose "${install_container_method}" -T \
    ${install_container_options} \
    "${install_container_name}" \
    ${OE_CONSOLE} oe:setup:shop \
    --db-host mysql \
    --db-port 3306 \
    --db-name example \
    --db-user root \
    --db-password root \
    --shop-url http://localhost.local/ \
    --shop-directory /var/www/source \
    --compile-directory /var/www/source/tmp

# Activate iDebug
if [ "${install_config_idebug}" == 'true' ]; then
    if [ -f source/source/config.inc.php ]; then
        perl -pi -e 's#iDebug = 0;#iDebug = -1;#g;' source/source/config.inc.php
    fi
    if [ -f source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php ]; then
        perl -pi -e 's#iDebug = 0;#iDebug = -1;#g;' source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php
    fi
fi

# Activate theme
if [ "${install_is_enterprise}" == 'true' ]; then
    docker compose "${install_container_method}" -T \
        ${install_container_options} \
        "${install_container_name}" \
        ${OE_CONSOLE} oe:theme:activate apex
fi

# Output PHP error log
if [ -s data/php/logs/error_log.txt ]; then
    echo -e "\033[0;35mPHP error log\033[0m"
    cat data/php/logs/error_log.txt
fi
exit 0
