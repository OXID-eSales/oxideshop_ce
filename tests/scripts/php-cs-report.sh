#!/bin/bash
# shellcheck disable=SC2013
# If there are multiple words in a line,  while building FILES, it doesn't hurt
# shellcheck disable=SC2086
# We want FILES to count as multiple arguments
set -e
env
set -x

PHPCS_DIFF_ONLY='true'
PHPCS_DIFF_FILTER='\.php$'

if [ "${PHPCS_DIFF_ONLY}" == "true" ]; then
    echo -e "\033[0;35m###  Use git diff for phpcs using filter '${PHPCS_DIFF_FILTER}' ###\033[0m"
    if [ "${GITHUB_EVENT_NAME}" == 'pull_request' ]; then
        URL="https://github.com/OXID-eSales/oxideshop_ce.git"
        git clone --depth 2 "${URL}" --branch ${GITHUB_BASE_REF} --single-branch .phpcs
        git -C .phpcs fetch origin ${GITHUB_REF}:tmp_pr
        git -C .phpcs checkout tmp_pr
    else
        URL="https://github.com/OXID-eSales/oxideshop_ce.git"
        git clone --depth 2 "${URL}" --branch "${GITHUB_REF_NAME}" --single-branch .phpcs
    fi
    git -C .phpcs diff --name-only --diff-filter=AM "${GITHUB_REF}" HEAD~1 | grep "${PHPCS_DIFF_FILTER}" | while read -r file; do
    if [[ -f "$file" ]]; then
        echo "$file"
    fi
    done >.changed-files.txt || true
    if [[ -f ".changed-files.txt" && -s ".changed-files.txt" ]]; then
        cat changed-files.txt
        FILES=""
        for FILE in $(cat .changed-files.txt); do
            FILES="${FILES} ${FILE}"
        done
    else
        echo "No files to scan"
        exit 0
    fi
    rm -rf .changed-files.txt .phpcs
    vendor/bin/phpcs \
        --standard=tests/phpcs.xml \
        --report=json \
        --report-file=tests/Reports/phpcs.report.json \
        ${FILES} \
    ||true
    # As the first one does not produce legible output, this gives us something to see in the log
    vendor/bin/phpcs \
        --standard=tests/phpcs.xml \
        --report=full \
        ${FILES}
else
    echo -e "\033[0;35m###  Use full file list for phpcs using filter '${PHPCS_DIFF_FILTER}' ###\033[0m"
    cd .phpcs
    find . -type f | grep "${PHPCS_DIFF_FILTER}" >changed-files.txt || true
    vendor/bin/phpcs \
        --standard=tests/phpcs.xml \
        --report=json \
        --report-file=tests/Reports/phpcs.report.json \
    ||true
    # As the first one does not produce legible output, this gives us something to see in the log
    vendor/bin/phpcs \
        --standard=tests/phpcs.xml \
        --report=full
fi

