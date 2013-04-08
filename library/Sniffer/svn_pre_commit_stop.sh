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
  $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/" && /bin/echo "!!! COMMIT STOP ON ALL BRANCHES !!!" 1>&2 && exit 1

# Committing to legacy branch (version 4.6) is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/" && /bin/echo "!!! COMMIT STOP ON 4.6 LEGACY BRANCH !!!" 1>&2 && exit 1

# Committing to maintenance branch (version 5.0) is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/" && /bin/echo "!!! COMMIT STOP ON 5.0 MAINTENANCE BRANCH !!!" 1>&2 && exit 1

# Committing to generic is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " generic\/" && /bin/echo "!!! COMMIT STOP ON GENERIC !!!" 1>&2 && exit 1

# Committing to pkgtools is not allowed
  $SVNLOOK changed -t "$TXN" "$REPOS" | grep " generic\/trunk\/pkgtools\/" && /bin/echo "!!! COMMIT STOP ON PKGTOOLS !!!" 1>&2 && exit 1

# Committing to trunk (version 5.1) is not allowed
  $SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/" && /bin/echo "!!! COMMIT STOP ON TRUNK !!!" 1>&2 && exit 1

# Committing to trunk/eshop/source is not allowed
# $SVNLOOK changed -t "$TXN" "$REPOS" | grep " trunk\/eshop\/source\/" && /bin/echo "!!! COMMIT STOP ON TRUNK/ESHOP/SOURCE - KEEPING SOURCE VERSION FOR 5.0.0 FINAL RELEASE. ASK DAINIUS IF NEED TO CHANGE SOMETHING!!!" 1>&2 && exit 1


# -----------------------
# Maintenance branch

# No GUI changes allowed on 5.0
 $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/application\/views\/azure\/tpl" && /bin/echo "!!! NO GUI CHANGES ON branches/5.0/eshop/source/application/views/azure/tpl" 1>&2 && exit 1
 $SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/out\/azure\/src" && /bin/echo "!!! NO GUI CHANGES ON branches/5.0/eshop/source/out/azure/src" 1>&2 && exit 1

# No DB changes allowed on 5.0
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_ee" && /bin/echo "!!! NO DB CHANGES ON branches/5.0/eshop/source/setup/sql_ee" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_pe" && /bin/echo "!!! NO DB CHANGES ON branches/5.0/eshop/source/setup/sql_pe" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/5.0\/eshop\/source\/setup\/sql_ce" && /bin/echo "!!! NO DB CHANGES ON branches/5.0/eshop/source/setup/sql_ce" 1>&2 && exit 1


# -----------------------
# Legacy branch

# No GUI changes allowed on 4.6
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/out\/azure\/tpl" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/tpl" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/out\/azure\/src" && /bin/echo "!!! NO GUI CHANGES ON branches/4.6/eshop/source/out/azure/src" 1>&2 && exit 1

# No DB changes allowed on 4.6
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_ee" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ee" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_pe" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_pe" 1>&2 && exit 1
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.6\/eshop\/source\/setup\/sql_ce" && /bin/echo "!!! NO DB CHANGES ON branches/4.6/eshop/source/setup/sql_ce" 1>&2 && exit 1



# -----------------------
# OLD branches, not used any more

# No commits to 4.5 branch
$SVNLOOK changed -t "$TXN" "$REPOS" | grep " branches\/4.5\/" && /bin/echo "!!! COMMIT STOP ON 4.5 BRANCH !!!" 1>&2 && exit 1


# All checks passed, so allow the commit.
exit 0