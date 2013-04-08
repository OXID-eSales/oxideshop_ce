#!/bin/bash

REPOS="$1"
TXN="$2"

TMPDIR="/tmp/svn_pre_commit_$TXN"
MYDIR=`dirname $0`
PHPCS="php -d memory_limit=512M -d include_path=$MYDIR $MYDIR/phpcs --report-width=120 -n --extensions=php --standard=Oxid"

bashtrap() {
    if ! test -z "`echo $TMPDIR | grep -e '^/tmp/svn_pre_commit'`" ; then
        if test -d $TMPDIR ; then
            rm -R $TMPDIR
        fi
    fi
}
trap bashtrap SIGINT SIGTERM EXIT

# two arguments - status code and message
exit_with_err() {
    if ! test "$1" -eq 0 ; then
        if ! test -z "$2" ; then
            echo "$2" >&2
        fi
        exit $1
    fi
    exit 0
}

if test -z "$REPOS" -o -z "$TXN" ; then
    exit_with_err 1 "Usage: $0 <repos path> <transaction>"
fi

mkdir -p $TMPDIR || exit_with_err 1 "can not create tmp dir"

SVNLOOK=`which svnlook`
if test -z "$SVNLOOK" ; then
    exit_with_err 1 "svnlook not found"
fi

$SVNLOOK changed -t "$TXN" "$REPOS" | grep -e '^[AUM]\ \{3\}' | \
grep 'eshop/source/' | \
perl -p \
-e 's@^.*eshop/source/core/3rd_party_licenses/.*$@@g;' \
-e 's@^.*eshop/source/core/adodblite/.*$@@g;' \
-e 's@^.*eshop/source/core/ccval/.*$@@g;' \
-e 's@^.*eshop/library/.*$@@g;' \
-e 's@^.*eshop/source/core/openid/.*$@@g;' \
-e 's@^.*eshop/source/core/facebook/.*$@@g;' \
-e 's@^.*eshop/source/core/phpmailer/.*$@@g;' \
-e 's@^.*eshop/source/core/tcpdf/.*$@@g;' \
-e 's@^.*eshop/source/core/smarty3?/(?!plugins/(.*\.)*ox).*$@@g;' \
-e 's@^.*eshop/source/core/smarty3?/plugins/oxemosadapter\.php@@g;' \
-e 's@^.*eshop/source/admin/dtaus/.*$@@g;' \
-e 's@^.*eshop/source/admin/reports/jpgraph/.*$@@g;' \
-e 's@^.*eshop/source/core/wysiwigpro/.*$@@g;' \
-e 's@^.*support/oxchkversion\.php$@@g;' \
-e 's@^.*eshop/source/core/oxserial\.php$@@g;' \
| grep -ve '^\$' | sed -e 's/^[A-Z]\ \{3\}\(.*\)$/\1/g' \
| while read f; do
    if ! test -z "$f" ; then
        tf="$TMPDIR/$f"
        mkdir -p `dirname "$tf"` || exit_with_err 1 "error while creating destination dir for '$tf'"
        $SVNLOOK cat -t $TXN $REPOS "$f" > "$tf" || exit_with_err 1 "can not get file $f from transaction $TXN in repos $REPOS (dest: $tf)"
    fi
done
# for st in ${PIPESTATUS[*]} ; do
#     if ! test "$st" -eq 0 ; then
#         exit_with_err 1 "error while retrieving commited files"
#     fi
# done

$PHPCS $TMPDIR 1>&2
RET=$?

exit $RET
