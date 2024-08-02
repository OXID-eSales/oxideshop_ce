#!/bin/bash
set -e

function init() {
    export XDEBUG_MODE=coverage

    # shellcheck disable=SC2128
    if [[ ${BASH_SOURCE} = */* ]]; then
        SCRIPT_DIR=${BASH_SOURCE%/*}/
    else
        SCRIPT_DIR=./
    fi
    if [ -z "${ABSOLUTE_PATH}" ]; then
        ABSOLUTE_PATH="$(pwd)"
    else
        ABSOLUTE_PATH="/var/www/${ABSOLUTE_PATH}"
    fi
    TESTDIR='tests'
    if [ ! -d "${ABSOLUTE_PATH}/${TESTDIR}" ]; then
        TESTDIR='Tests'
        if [ ! -d "${ABSOLUTE_PATH}/${TESTDIR}" ]; then
            echo -e "\033[0;31m###  Could not find folder tests or Tests in ${ABSOLUTE_PATH} ###\033[0m"
            exit 1
        fi
    fi

    [[ ! -d "${ABSOLUTE_PATH}/${TESTDIR}/Output" ]] && mkdir "${ABSOLUTE_PATH}/${TESTDIR}/Output"
    [[ ! -d "${ABSOLUTE_PATH}/${TESTDIR}/Output" ]] && mkdir "${ABSOLUTE_PATH}/${TESTDIR}/Reports"

    OUTPUT_DIR="${ABSOLUTE_PATH}/${TESTDIR}/Output"
    REPORT_DIR="${ABSOLUTE_PATH}/${TESTDIR}/Reports"

    if [ -z "${SELENIUM_SERVER_HOST}" ]; then
        SELENIUM_SERVER_HOST='selenium'
    fi

    if [ -z "${SUITE}" ]; then
        SUITE="AllTestsUnit"
    fi
    LOG_FILE="${OUTPUT_DIR}/deprecated_tests.txt"
    PATTERN_FILE="${SCRIPT_DIR}codeception_failure_pattern.txt"

    RUNTEST="vendor/bin/runtests"
    if [ ! -f "${RUNTEST}" ]; then
        RUNTEST="/var/www/${RUNTEST}"
        if [ ! -f "${RUNTEST}" ]; then
            echo -e "\033[0;31mCould not find runtests in vendor/bin or /var/www/vendor/bin\033[0m"
            exit 1
        fi
    fi

    cat <<EOF
        Path: ${ABSOLUTE_PATH}
        Script directory: ${SCRIPT_DIR}
        Output directory: ${OUTPUT_DIR}
        Report directory: ${REPORT_DIR}
        Selenium host: ${SELENIUM_SERVER_HOST}
        Suite: ${SUITE}
        Runtest: ${RUNTEST}
        Log file: ${LOG_FILE}
        Failure patterns: ${PATTERN_FILE}
EOF
}

init
cp vendor/oxid-esales/testing-library/test_config.yml.dist test_config.yml
"${RUNTEST}" \
    --coverage-clover=${REPORT_DIR}/coverage_deprecated_tests.xml "${SUITE}" 2>&1 | tee "${LOG_FILE}"
RESULT=$?
echo "runtest exited with error code ${RESULT}"

"${SCRIPT_DIR}check_log.sh" "${LOG_FILE}" "${PATTERN_FILE}"
