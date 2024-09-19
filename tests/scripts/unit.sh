#!/bin/bash
set -e
export XDEBUG_MODE=coverage
function init() {
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

    if [ -z "${SUITE}" ]; then
        SUITE="${ABSOLUTE_PATH}/${TESTDIR}/Unit"
    fi

    LOG_FILE="${OUTPUT_DIR}/phpunit_unit.txt"
    PATTERN_FILE="${SCRIPT_DIR}unit_failure_pattern.txt"

    PHPUNIT="vendor/bin/phpunit"
    if [ ! -f "${PHPUNIT}" ]; then
        PHPUNIT="/var/www/${PHPUNIT}"
        if [ ! -f "${PHPUNIT}" ]; then
            echo -e "\033[0;31mCould not find phpunit in vendor/bin or /var/www/vendor/bin\033[0m"
            exit 1
        fi
    fi

    BOOTSTRAP="/var/www/source/bootstrap.php"
    if [ ! -f "${BOOTSTRAP}" ]; then
        BOOTSTRAP="/var/www/vendor/oxid-esales/oxideshop-ce/tests/bootstrap.php"
        if [ ! -f "${BOOTSTRAP}" ]; then
            echo -e "\033[0;31mCould not find bootstrap.php in /var/www/tests or /var/www/oxid-esales/oxideshop-ce/tests\033[0m"
            find /var/www -iname "bootstrap.php"
            exit 1
        fi
    fi

    XML_FILE="${ABSOLUTE_PATH}/phpunit.xml"
    COVERAGE_FILE="${REPORT_DIR}/coverage_phpunit_unit.xml"

    cat <<EOF
        Path: ${ABSOLUTE_PATH}
        Script directory: ${SCRIPT_DIR}
        Output directory: ${OUTPUT_DIR}
        Report directory: ${REPORT_DIR}
        Suite: ${SUITE}
        Bootstrap: ${BOOTSTRAP}
        Config: ${XML_FILE}
        Phpunit: ${PHPUNIT}
        Coverage: ${COVERAGE_FILE}
        Log file: ${LOG_FILE}
        Failure patterns: ${PATTERN_FILE}
EOF
}

init

"${PHPUNIT}" \
    -c "${XML_FILE}" \
    --bootstrap "${BOOTSTRAP}" \
    --coverage-clover="${COVERAGE_FILE}" \
    ${UNIT_OPTIONS} \
    "${SUITE}" 2>&1 \
| tee "${LOG_FILE}"
RESULT=$?
echo "phpunit exited with error code ${RESULT}"
"${SCRIPT_DIR}check_log.sh" "${LOG_FILE}" "${PATTERN_FILE}"
