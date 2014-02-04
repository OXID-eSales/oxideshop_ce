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
 * Class manages discount users
 */
class discount_users_ajax extends ajaxListComponent
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
                                        array( 'oxid',     'oxobject2discount', 0, 0, 1 ),
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oConfig = $this->getConfig();

        $sUserTable = $this->_getViewName( 'oxuser' );
        $oDb = oxDb::getDb();
        $sId = $oConfig->getRequestParameter( 'oxid' );
        $sSynchId = $oConfig->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sId ) {
            $sQAdd = " from $sUserTable where 1 ";
            if (!$oConfig->getConfigParam( 'blMallUsers' ) )
                $sQAdd .= " and oxshopid = '".$oConfig->getShopId()."' ";
        } else {
            // selected group ?
            if ( $sSynchId && $sSynchId != $sId ) {
                $sQAdd = " from oxobject2group left join $sUserTable on $sUserTable.oxid = oxobject2group.oxobjectid where oxobject2group.oxgroupsid = ".$oDb->quote( $sId );
                if ( !$oConfig->getConfigParam( 'blMallUsers' ) )
                    $sQAdd .= " and $sUserTable.oxshopid = '".$oConfig->getShopId()."' ";

            } else {
                $sQAdd  = " from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid ";
                $sQAdd .= " and oxobject2discount.oxdiscountid = ".$oDb->quote( $sId )." and oxobject2discount.oxtype = 'oxuser' ";
            }
        }

        if ( $sSynchId && $sSynchId != $sId ) {
            $sQAdd .= " and $sUserTable.oxid not in ( select $sUserTable.oxid from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid ";
            $sQAdd .= " and oxobject2discount.oxdiscountid = ".$oDb->quote( $sSynchId )." and oxobject2discount.oxtype = 'oxuser' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user from discount config
     *
     * @return null
     */
    public function removeDiscUser()
    {
        $oConfig = $this->getConfig();

        $aRemoveGroups = $this->_getActionIds( 'oxobject2discount.oxid' );
        if ( $oConfig->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2discount.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxobject2discount where oxobject2discount.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aRemoveGroups ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds user to discount config
     *
     * @return null
     */
    public function addDiscUser()
    {
        $oConfig = $this->getConfig();
        $aChosenUsr = $this->_getActionIds( 'oxuser.oxid' );
        $soxId       = $oConfig->getRequestParameter( 'synchoxid');


        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sUserTable = $this->_getViewName( 'oxuser' );
            $aChosenUsr = $this->_getAll( $this->_addFilter( "select $sUserTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenUsr ) ) {
            foreach ( $aChosenUsr as $sChosenUsr) {
                $oObject2Discount = oxNew( "oxbase" );
                $oObject2Discount->init( 'oxobject2discount' );
                $oObject2Discount->oxobject2discount__oxdiscountid = new oxField($soxId);
                $oObject2Discount->oxobject2discount__oxobjectid   = new oxField($sChosenUsr);
                $oObject2Discount->oxobject2discount__oxtype       = new oxField("oxuser");
                $oObject2Discount->save();
            }
        }
    }
}
