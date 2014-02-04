<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class manages users assignment to groups
 */
class usergroup_main_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,  visible, multilanguage, ident
                                        array( 'oxusername',  'oxuser', 1, 0, 0 ),
                                        array( 'oxlname',     'oxuser', 0, 0, 0 ),
                                        array( 'oxfname',     'oxuser', 0, 0, 0 ),
                                        array( 'oxstreet',    'oxuser', 0, 0, 0 ),
                                        array( 'oxstreetnr',  'oxuser', 0, 0, 0 ),
                                        array( 'oxcity',      'oxuser', 0, 0, 0 ),
                                        array( 'oxzip',       'oxuser', 0, 0, 0 ),
                                        array( 'oxfon',       'oxuser', 0, 0, 0 ),
                                        array( 'oxbirthdate', 'oxuser', 0, 0, 0 ),
                                        array( 'oxid',        'oxuser', 0, 0, 1 ),
                                        ),
                                    'container2' => array(
                                        array( 'oxusername',  'oxuser', 1, 0, 0 ),
                                        array( 'oxlname',     'oxuser', 0, 0, 0 ),
                                        array( 'oxfname',     'oxuser', 0, 0, 0 ),
                                        array( 'oxstreet',    'oxuser', 0, 0, 0 ),
                                        array( 'oxstreetnr',  'oxuser', 0, 0, 0 ),
                                        array( 'oxcity',      'oxuser', 0, 0, 0 ),
                                        array( 'oxzip',       'oxuser', 0, 0, 0 ),
                                        array( 'oxfon',       'oxuser', 0, 0, 0 ),
                                        array( 'oxbirthdate', 'oxuser', 0, 0, 0 ),
                                        array( 'oxid',     'oxobject2group', 0, 0, 1 ),
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();

        // looking for table/view
        $sUserTable = $this->_getViewName( 'oxuser' );
        $oDb = oxDb::getDb();
        $sRoleId      = oxConfig::getParameter( 'oxid' );
        $sSynchRoleId = oxConfig::getParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sRoleId ) {
            $sQAdd  = " from $sUserTable where 1 ";
        } else {
            $sQAdd  = " from $sUserTable, oxobject2group where $sUserTable.oxid=oxobject2group.oxobjectid and ";
            $sQAdd .= " oxobject2group.oxgroupsid = ".$oDb->quote( $sRoleId );
        }

        if ( $sSynchRoleId && $sSynchRoleId != $sRoleId) {
            $sQAdd .= " and $sUserTable.oxid not in ( select $sUserTable.oxid from $sUserTable, oxobject2group where $sUserTable.oxid=oxobject2group.oxobjectid and ";
            $sQAdd .= " oxobject2group.oxgroupsid = ".$oDb->quote( $sSynchRoleId );
            if (!$myConfig->getConfigParam( 'blMallUsers' ) )
                $sQAdd .= " and $sUserTable.oxshopid = '".$myConfig->getShopId()."' ";
            $sQAdd .= " ) ";
        }

        if ( !$myConfig->getConfigParam( 'blMallUsers' ) )
            $sQAdd .= " and $sUserTable.oxshopid = '".$myConfig->getShopId()."' ";

        return $sQAdd;
    }

    /**
     * Removes User from group
     *
     * @return null
     */
    public function removeUserFromUGroup()
    {
        $aRemoveGroups = $this->_getActionIds( 'oxobject2group.oxid' );

        if ( oxConfig::getParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2group.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxobject2group where oxobject2group.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aRemoveGroups ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds User to group
     *
     * @return null
     */
    public function addUserToUGroup()
    {
        $aAddUsers = $this->_getActionIds( 'oxuser.oxid' );
        $soxId     = oxConfig::getParameter( 'synchoxid' );

        if ( oxConfig::getParameter( 'all' ) ) {
            $sUserTable = $this->_getViewName( 'oxuser' );
            $aAddUsers = $this->_getAll( $this->_addFilter( "select $sUserTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aAddUsers ) ) {
            foreach ($aAddUsers as $sAdduser) {
                $oNewGroup = oxNew( "oxobject2group" );
                $oNewGroup->oxobject2group__oxobjectid = new oxField($sAdduser);
                $oNewGroup->oxobject2group__oxgroupsid = new oxField($soxId);
                $oNewGroup->save();
            }
        }
    }
}
