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
 * Class manages delivery groups
 */
class delivery_groups_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,  visible, multilanguage, ident
                                        array( 'oxtitle',  'oxgroups', 1, 0, 0 ),
                                        array( 'oxid',     'oxgroups', 0, 0, 0 ),
                                        array( 'oxid',     'oxgroups', 0, 0, 1 ),
                                        ),
                                    'container2' => array(
                                        array( 'oxtitle',  'oxgroups', 1, 0, 0 ),
                                        array( 'oxid',     'oxgroups', 0, 0, 0 ),
                                        array( 'oxid',     'oxobject2delivery', 0, 0, 1 ),
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
        $oDb = oxDb::getDb();

        // active AJAX component
        $sGroupTable = $this->_getViewName('oxgroups');

        $sId      = $myConfig->getRequestParameter( 'oxid' );
        $sSynchId = $myConfig->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sId ) {
            $sQAdd  = " from $sGroupTable where 1 ";
        } else {
            $sQAdd  = " from oxobject2delivery left join $sGroupTable on $sGroupTable.oxid=oxobject2delivery.oxobjectid ";
            $sQAdd .= " where oxobject2delivery.oxdeliveryid = ".$oDb->quote( $sId )." and oxobject2delivery.oxtype = 'oxgroups' ";
        }

        if ( $sSynchId && $sSynchId != $sId ) {
            $sQAdd .= " and $sGroupTable.oxid not in ( select $sGroupTable.oxid from oxobject2delivery left join $sGroupTable on $sGroupTable.oxid=oxobject2delivery.oxobjectid ";
            $sQAdd .= " where oxobject2delivery.oxdeliveryid = ".$oDb->quote( $sSynchId )." and oxobject2delivery.oxtype = 'oxgroups' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user group from delivery configuration
     *
     * @return null
     */
    public function removeGroupFromDel()
    {
        $aRemoveGroups = $this->_getActionIds( 'oxobject2delivery.oxid' );
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2delivery.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aRemoveGroups ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds user group to delivery configuration
     *
     * @return null
     */
    public function addGroupToDel()
    {
        $aChosenCat = $this->_getActionIds( 'oxgroups.oxid' );
        $soxId      = $this->getConfig()->getRequestParameter( 'synchoxid' );

        // adding
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aChosenCat = $this->_getAll( $this->_addFilter( "select $sGroupTable.oxid ".$this->_getQuery() ) );
        }

        if ( $soxId && $soxId != "-1" && is_array( $aChosenCat ) ) {
            foreach ( $aChosenCat as $sChosenCat) {
                $oObject2Delivery = oxNew( 'oxbase' );
                $oObject2Delivery->init( 'oxobject2delivery' );
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid   = new oxField($sChosenCat);
                $oObject2Delivery->oxobject2delivery__oxtype       = new oxField('oxgroups');
                $oObject2Delivery->save();
            }
        }
    }
}
