#!/bin/bash
# shellcheck disable=SC2013
# If there are multiple words in a line,  while building FILES, it doesn't hurt
# shellcheck disable=SC2086
# We want FILES to count as multiple arguments
set -e
PHPCS_DIFF_ONLY='true'
PHPCS_DIFF_FILTER='\.php$'
if [ "${PHPCS_DIFF_ONLY}" == "true" ]; then
    echo -e "\033[0;35m###  Use git diff for phpcs using filter '${PHPCS_DIFF_FILTER}' ###\033[0m"
    git diff --name-only --diff-filter=AM "${GITHUB_REF}" HEAD~1 | grep "${PHPCS_DIFF_FILTER}" | while read -r file; do
    if [[ -f "$file" ]]; then
        echo "$file"
    fi
    done >.changed-files.txt || true
else
    echo -e "\033[0;35m###  Use full file list for phpcs using filter '${PHPCS_DIFF_FILTER}' ###\033[0m"
    find . -type f | grep "${PHPCS_DIFF_FILTER}" >changed-files.txt || true
fi
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
