#!/bin/bash
#
# Usage:
# check_log.sh path/to/log path/to/failure/patterns
# check_log checks if a given log file is not empty or contains patterns that are
# listed in the pattern file.
# It requires two inputs:
# The first input is the name of the log file to check, and the second input is the
# pattern file, where every line contains a grep -E compatible pattern to search for
# The script returns 0, if the log file exists, is not empty and does not contain any
# of the patterns in the pattern file. It returns 1 otherwise

LOG_FILE="${1}"
PATTERN_FILE="${2}"
RESULT=0

if [ ! -e "${LOG_FILE}" ]; then
    echo -e "\033[0;31mLog file '${LOG_FILE}' does not exist! Seems like no tests have been run!\033[0m"
    RESULT=1
fi

if [ ! -s "${LOG_FILE}" ]; then
    echo -e "\033[0;31mLog file '${LOG_FILE}' is empty! Seems like no tests have been run!\033[0m"
    RESULT=1
fi
if [ ! -f "${PATTERN_FILE}" ]; then
    echo -e "\033[0;31mPattern file '${PATTERN_FILE}' does not exist!\033[0m"
    RESULT=1
fi
[[ ${RESULT} -gt 0 ]] && exit 1

# shellcheck disable=SC2016
sed -e 's|(.*)\r|$1|' -i "${PATTERN_FILE}"
while read -r LINE ; do
    if [ -n "${LINE}" ]; then
        if grep -q -E "${LINE}" "${LOG_FILE}"; then
            echo -e "\033[0;31m Log contains matching pattern ${LINE}\033[0m"
            grep -E "${LINE}" "${LOG_FILE}"
            RESULT=1
        else
            echo -e "\033[0;32m Log does not contain matching pattern ${LINE}"
        fi
    fi
done <"${PATTERN_FILE}"

if [ -s "/var/sync/logs/error_log.txt" ]; then
    echo -e "\033[0;31mPHP error log is not empty!\033[0m"
    cat /var/sync/logs/error_log.txt
    RESULT=1
fi

exit ${RESULT}
