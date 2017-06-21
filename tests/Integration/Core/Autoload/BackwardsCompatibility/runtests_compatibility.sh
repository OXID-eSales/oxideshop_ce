#!/bin/bash
SCRIPTDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BASEDIR="$(dirname $(dirname $(dirname $(dirname $(dirname "${SCRIPTDIR}")))))/"
VENDORDIR="${BASEDIR}vendor/"
TESTDIR="${BASEDIR}tests/"
for file in "${TESTDIR}Integration/Core/Autoload/BackwardsCompatibility/"*Test.php; do
  echo "--------------------------------------------------------------------------------"
  echo "${file}"
  "${VENDORDIR}bin/runtests" "${file}"
  echo ""
done
