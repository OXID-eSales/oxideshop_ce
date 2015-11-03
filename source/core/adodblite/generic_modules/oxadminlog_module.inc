<?php
eval( 'class mysql_oxadminlog_EXTENDER extends '. $last_module . '_ADOConnection { }' );
class oxadminlog_ADOConnection extends mysql_oxadminlog_EXTENDER
{
    public function &Execute( $sql, $inputarr = false )
    {
        $sSearch = strtolower( ltrim($sql) );
        if ( ( strpos( $sSearch, 'insert into' ) === 0 || strpos( $sSearch, 'update ' ) === 0 || strpos( $sSearch, 'delete ' ) === 0 ) &&
              strpos( $sSearch, 'oxsession' ) === false && strpos( $sSearch, 'oxadminlog' ) === false && strpos( $sSearch, 'oxcache' ) === false ) {

            $blInsert = true;

            $myConfig = oxRegistry::getConfig();
            if ( is_array( $aLogSkipTags = $myConfig->getConfigParam( 'aLogSkipTags' ) ) ) {
                foreach( $aLogSkipTags as $sSkipThis ) {
                    if( strpos( $sSearch, $sSkipThis ) !== false ) {
                       $blInsert = false;
                       break;
                    }
                }
            }

            if ( $blInsert && $sSearch ) {

                $oUser = oxNew( 'oxuser' );
                if ( $oUser->loadAdminUser() ) {
                    $sUserId = $oUser->getId();
                } else {
                    $sUserId = '';
                }


                $sQ = "insert into oxadminlog ( oxuserid, oxsql )
                       values ( '{$sUserId}', " . $this->quote( $sql ) . " ) ";

                // write information
                parent::Execute( $sQ );
            }
        }
        return parent::Execute( $sql, $inputarr );
    }
}

eval( 'class mysql_oxadminlog_resultset_EXTENDER extends '. $last_module . '_ResultSet { }' );
class oxadminlog_ResultSet extends mysql_oxadminlog_resultset_EXTENDER {}