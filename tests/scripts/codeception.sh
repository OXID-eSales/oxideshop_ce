#!/bin/bash
set -e
set -x
SUITE="${1}"
SHARD_STEP="${2}"
SHARD_COUNT="${3}"

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
    TEST_DIR='tests'
    if [ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}" ]; then
        TEST_DIR='Tests'
        if [ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}" ]; then
            echo -e "\033[0;31m###  Could not find folder tests or Tests in ${ABSOLUTE_PATH} ###\033[0m"
            exit 1
        fi
    fi

    [[ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}/Output" ]] && mkdir "${ABSOLUTE_PATH}/${TEST_DIR}/Output"
    [[ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}/Reports" ]] && mkdir "${ABSOLUTE_PATH}/${TEST_DIR}/Reports"

    OUTPUT_DIR="${ABSOLUTE_PATH}/${TEST_DIR}/Output"
    REPORT_DIR="${ABSOLUTE_PATH}/${TEST_DIR}/Reports"

    if [ -z "${SELENIUM_SERVER_HOST}" ]; then
        export SELENIUM_SERVER_HOST=selenium
    fi

    if [ -z "${SUITE}" ]; then
        SUITE="Acceptance"
        if [ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}/Codeception/${SUITE}" ]; then
            SUITE="acceptance"
            if [ ! -d "${ABSOLUTE_PATH}/${TEST_DIR}/Codeception/${SUITE}" ]; then
                echo -e "\033[0;31mCould not find suite Acceptance or acceptance in ${TEST_DIR}/Codeception\033[0m"
                exit 1
            fi
        fi
    fi
    if [ -n "${SHARD_STEP}" ]; then
        LOG_FILE="${OUTPUT_DIR}/codeception_${SUITE}_shard_${SHARD_STEP}.txt"
    else
        LOG_FILE="${OUTPUT_DIR}/codeception_${SUITE}.txt"
    fi
    PATTERN_FILE="${SCRIPT_DIR}codeception_failure_pattern.txt"

    CODECEPTION="vendor/bin/codecept"
    if [ ! -f "${CODECEPTION}" ]; then
        CODECEPTION="/var/www/${CODECEPTION}"
        if [ ! -f "${CODECEPTION}" ]; then
            echo -e "\033[0;31mCould not find codecept in vendor/bin or /var/www/vendor/bin\033[0m"
            exit 1
        fi
    fi

    if [ -z "${SHARD_STEP}${SHARDS_COUNT}" ]; then
        SHARDING="Sharding: Off"
    else
        SHARDING="Sharding: ${SHARD_STEP}/${SHARD_COUNT}"
        if [[ ! ${SHARD_STEP} =~ ^[0-9]+$ ]]; then
            echo "Argument 2 (SHARD_STEP) must be numerical"
            exit 1
        fi
        if [[ ! ${SHARD_COUNT} =~ ^[0-9]+$ ]]; then
            echo "Argument 3 (SHARD_COUNT) must be numerical"
            exit 1
        fi
        if [ ${SHARD_STEP} -gt ${SHARD_COUNT} ]; then
            echo "Argument 2 (SHARD_STEP) must less or equal to shards count"
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
        Codeception: ${CODECEPTION}
        ${SHARDING}
        Log file: ${LOG_FILE}
        Failure patterns: ${PATTERN_FILE}
EOF
}

# wait for selenium host
function wait_for_selenium() {
    local I=60
    until [ $I -le 0 ]; do
        curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status" | grep '"ready": true' && break
        echo "."
        sleep 1
        ((I--))
    done
    set -e
    curl -sSjkL "http://${SELENIUM_SERVER_HOST}:4444/wd/hub/status"
}

init
wait_for_selenium

"${CODECEPTION}" build -c "${ABSOLUTE_PATH}/${TEST_DIR}/codeception.yml"
RESULT=$?
echo "Codeception build exited with error code ${RESULT}"

if [[ -n "${SHARD_STEP}" && -n "${SHARD_COUNT}" ]]; then
    "${CODECEPTION}" run "${SUITE}" \
        -c "${ABSOLUTE_PATH}/${TEST_DIR}/codeception.yml" \
        ${CODECEPTION_OPTIONS} \
        -o "paths: output: ${OUTPUT_DIR}" \
        --shard ${SHARD_STEP}/${SHARD_COUNT} 2>&1 |
        tee "${LOG_FILE}"
    RESULT=$?
    echo "Codeception shard ${SHARD_STEP} run exited with error code ${RESULT}"
else
    "${CODECEPTION}" run "${SUITE}" \
        -c "${ABSOLUTE_PATH}/${TEST_DIR}/codeception.yml" \
        ${CODECEPTION_OPTIONS} \
        -o "paths: output: ${OUTPUT_DIR}" 2>&1 |
        tee "${LOG_FILE}"
    RESULT=$?
    echo "Codeception run exited with error code ${RESULT}"
fi
"${SCRIPT_DIR}check_log.sh" "${LOG_FILE}" "${PATTERN_FILE}"
