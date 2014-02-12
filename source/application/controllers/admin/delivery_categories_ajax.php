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
 * Class manages delivery categories
 */
class delivery_categories_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle', 'oxcategories', 1, 1, 0 ),
                                        array( 'oxdesc',  'oxcategories', 1, 1, 0 ),
                                        array( 'oxid',    'oxcategories', 0, 0, 0 ),
                                        array( 'oxid',    'oxcategories', 0, 0, 1 )
                                        ),
                                'container2' => array(
                                        array( 'oxtitle', 'oxcategories', 1, 1, 0 ),
                                        array( 'oxdesc',  'oxcategories', 1, 1, 0 ),
                                        array( 'oxid',    'oxcategories', 0, 0, 0 ),
                                        array( 'oxid',    'oxobject2delivery', 0, 0, 1 ),
                                        array( 'oxid',    'oxcategories',      0, 0, 1 )
                                        ),
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        // looking for table/view
        $sCatTable = $this->_getViewName('oxcategories');
        $oDb = oxDb::getDb();
        $sDelId      = $this->getConfig()->getRequestParameter( 'oxid' );
        $sSynchDelId = $this->getConfig()->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sDelId) {
            $sQAdd  = " from $sCatTable ";
        } else {
            $sQAdd  = " from oxobject2delivery left join $sCatTable on $sCatTable.oxid=oxobject2delivery.oxobjectid ";
            $sQAdd .= " where oxobject2delivery.oxdeliveryid = ".$oDb->quote( $sDelId )." and oxobject2delivery.oxtype = 'oxcategories' ";
        }

        if ( $sSynchDelId && $sSynchDelId != $sDelId) {
            // dodger performance
            $sSubSelect  = " select $sCatTable.oxid from oxobject2delivery left join $sCatTable on $sCatTable.oxid=oxobject2delivery.oxobjectid ";
            $sSubSelect .= " where oxobject2delivery.oxdeliveryid = ".$oDb->quote( $sSynchDelId )." and oxobject2delivery.oxtype = 'oxcategories' ";
            if ( stristr( $sQAdd, 'where' ) === false )
                $sQAdd .= ' where ';
            else
                $sQAdd .= ' and ';
            $sQAdd .= " $sCatTable.oxid not in ( $sSubSelect ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes category from delivery configuration
     *
     * @return null
     */
    public function removeCatFromDel()
    {
        $aChosenCat = $this->_getActionIds( 'oxobject2delivery.oxid' );

        // removing all
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2delivery.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenCat ) ) {
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenCat ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds category to delivery configuration
     *
     * @return null
     */
    public function addCatToDel()
    {
        $aChosenCat = $this->_getActionIds( 'oxcategories.oxid' );
        $soxId      = $this->getConfig()->getRequestParameter( 'synchoxid');

        // adding
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sCatTable = $this->_getViewName('oxcategories');
            $aChosenCat = $this->_getAll( $this->_addFilter( "select $sCatTable.oxid ".$this->_getQuery() ) );
        }

        if ( isset( $soxId) && $soxId != "-1" && isset( $aChosenCat) && $aChosenCat) {
            foreach ( $aChosenCat as $sChosenCat) {
                $oObject2Delivery = oxNew( 'oxbase' );
                $oObject2Delivery->init( 'oxobject2delivery' );
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid   = new oxField($sChosenCat);
                $oObject2Delivery->oxobject2delivery__oxtype       = new oxField("oxcategories");
                $oObject2Delivery->save();
            }
        }
    }
}
