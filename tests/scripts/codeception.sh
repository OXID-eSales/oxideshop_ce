#!/bin/bash
set -e
SUITE="${1}"

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

    if [ -z "${SELENIUM_SERVER_HOST}" ]; then
        export SELENIUM_SERVER_HOST=selenium
    fi

    if [ -z "${SUITE}" ]; then
        SUITE="Acceptance"
        if [ ! -d "${ABSOLUTE_PATH}/${TESTDIR}/Codeception/${SUITE}" ]; then
            SUITE="acceptance"
            if [ ! -d "${ABSOLUTE_PATH}/${TESTDIR}/Codeception/${SUITE}" ]; then
                echo -e "\033[0;31mCould not find suite Acceptance or acceptance in ${TESTDIR}/Codeception\033[0m"
                exit 1
            fi
        fi
    fi
    LOG_FILE="${OUTPUT_DIR}/codeception_${SUITE}.txt"
    PATTERN_FILE="${SCRIPT_DIR}codeception_failure_pattern.txt"

    CODECEPT="vendor/bin/codecept"
    if [ ! -f "${CODECEPT}" ]; then
        CODECEPT="/var/www/${CODECEPT}"
        if [ ! -f "${CODECEPT}" ]; then
            echo -e "\033[0;31mCould not find codecept in vendor/bin or /var/www/vendor/bin\033[0m"
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
        Codeception: ${CODECEPT}
        Log file: ${LOG_FILE}
        Failure patterns: ${PATTERN_FILE}
EOF
}

# wait for selenium host
function wait_for_selenium() {
    local I=60
    until  [ $I -le 0 ]; do
        curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status" |grep '"ready": true' && break
        echo "."
        sleep 1
        ((I--))
    done
    set -e
    curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status"
}

init
wait_for_selenium

"${CODECEPT}" build -c "${ABSOLUTE_PATH}/${TESTDIR}/codeception.yml"
RESULT=$?
echo "Codecept build exited with error code ${RESULT}"
"${CODECEPT}" run "${SUITE}" \
    -c "${ABSOLUTE_PATH}/${TESTDIR}/codeception.yml" \
    --ext DotReporter \
    -o "paths: output: ${OUTPUT_DIR}" 2>&1 \
| tee "${LOG_FILE}"
RESULT=$?
echo "Codecept run exited with error code ${RESULT}"
"${SCRIPT_DIR}check_log.sh" "${LOG_FILE}" "${PATTERN_FILE}"
