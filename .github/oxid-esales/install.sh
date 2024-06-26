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
    if [ -z "${OXID_BUILD_DIRECTORY}" ]; then
      echo "OXID_BUILD_DIRECTORY is not set, setting it to /var/www/source/tmp"
      export OXID_BUILD_DIRECTORY="/var/www/source/tmp"
    else
      echo "OXID_BUILD_DIRECTORY is set to '${OXID_BUILD_DIRECTORY}'"
    fi
    if [ ! -d "${OXID_BUILD_DIRECTORY}" ]; then
      echo "Creating '${OXID_BUILD_DIRECTORY}'"
      docker compose "${install_container_method}" -T \
        ${install_container_options} \
        "${install_container_name}" \
        mkdir -p "${OXID_BUILD_DIRECTORY}"
    fi
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
    --compile-directory "${OXID_BUILD_DIRECTORY}"

if [ -d source/vendor/oxid-esales/oxideshop-ce ]; then
    # Handle copying of the config
    if [ -f source/source/config.inc.php.dist ] && [ -f source/source/config.inc.php ]; then
        if diff -q source/source/config.inc.php.dist source/source/config.inc.php; then
            echo "source/config.inc.php has not been modified"
            TARGET=source/config.inc.php    
        else
            echo "Config file is source/config.inc.php"
            CONFIG_FILE=source/config.inc.php
        fi
    else
        echo "source/config.inc.php does not exist"
        TARGET=source/config.inc.php
    fi
    if [ -f source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php.dist ] && [ -f source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php ]; then
        if diff -q source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php.dist source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php; then
            echo "vendor/oxid-esales/oxideshop-ce/source/config.inc.php has not been modified"
            if [ -n "${TARGET}" ]; then
                echo "ERROR: Neither source/config.inc.php nor vendor/oxid-esales/oxideshop-ce/source/config.inc.php have been updated"
                exit 1
            fi
            TARGET=source/config.inc.php
        else
            if [ -n "${CONFIG_FILE}" ]; then
            echo "ERROR: Both source/config.inc.php and vendor/oxid-esales/oxideshop-ce/source/config.inc.php have been updated"
            exit 1
            fi
            echo "Config file is source/vendor/oxid-esales/oxideshop-ce/source/config.inc.php"
            CONFIG_FILE=vendor/oxid-esales/oxideshop-ce/source/config.inc.php
        fi
    else
        if [ -n "${TARGET}" ]; then
            echo "ERROR: Neither vendor/oxid-esales/oxideshop-ce/source/config.inc.php nor source/config.inc.php have been updated"
            exit 1
        fi
        TARGET=source/config.inc.php
    fi
    cp "source/${SOURCE}" "source/${TARGET}"
else
    echo "vendor/oxid-esales/oxideshop-ce does not exist, assuming conventional shop install"
fi

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
docker compose "${install_container_method}" -T \
    ${install_container_options} \
    "${install_container_name}" \
    ${OE_CONSOLE} oe:theme:activate apex

# Output PHP error log
if [ -s data/php/logs/error_log.txt ]; then
    echo -e "\033[0;35mPHP error log\033[0m"
    cat data/php/logs/error_log.txt
fi
exit 0
