#!/bin/bash

REPOS="$1"
TXN="$2"

SVNLOOK=`which svnlook`
if test -z "$SVNLOOK" ; then
    exit_with_err 1 "svnlook not found"
fi

# Exception for hooks
$SVNLOOK changed -t "$TXN" "$REPOS" | grep "svn_pre_commit" 1>&2 && exit 0




# Committing to branches is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/" && /bin/echo "!!! COMMIT STOP ON BRANCHES !!!" 1>&2 && exit 1

# Committing to maintenance is not allowed
 $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/" && /bin/echo "!!! COMMIT STOP ON MAINTENANCE BRANCH !!!" 1>&2 && exit 1

# Committing to generic is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " generic\/" && /bin/echo "!!! COMMIT STOP ON GENERIC !!!" 1>&2 && exit 1

# Committing to trunk is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/" && /bin/echo "!!! COMMIT STOP ON TRUNK !!!" 1>&2 && exit 1


# No GUI changes allowed on development
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/application\/views\/azure\/tpl" && /bin/echo "!!! NO GUI CHANGES ON trunk/eshop/source/application/views/azure/tpl" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/out\/azure\/src" && /bin/echo "!!! NO GUI CHANGES ON trunk/eshop/source/out/azure/src" 1>&2 && exit 1

# No DB changes allowed on development
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/setup\/sql_ee" && /bin/echo "!!! NO DB CHANGES ON trunk/eshop/source/setup/sql_ee" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/setup\/sql_pe" && /bin/echo "!!! NO DB CHANGES ON trunk/eshop/source/setup/sql_pe" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/setup\/sql_ce" && /bin/echo "!!! NO DB CHANGES ON trunk/eshop/source/setup/sql_ce" 1>&2 && exit 1

# No GUI changes allowed on maintenance
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/out\/azure\/tpl" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/tpl" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/out\/azure\/src" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/src" 1>&2 && exit 1

# No DB changes allowed on maintenance
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_ee" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ee" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_pe" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_pe" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_ce" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ce" 1>&2 && exit 1



# No GUI changes allowed on maintenance
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/out\/azure\/tpl" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/tpl" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/out\/azure\/src" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/src" 1>&2 && exit 1

# No DB changes allowed on maintenance
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_ee" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ee" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_pe" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_pe" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_ce" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ce" 1>&2 && exit 1





# old branches not used any more
# Committing to branches is not allowed
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.5\/" && /bin/echo "!!! THIS BRANCH IS NOT USED ANY MORE !!!" 1>&2 && exit 1





# All checks passed, so allow the commit.
exit 0