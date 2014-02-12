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
 * Class manages deliveryset and delivery configuration
 */
class deliveryset_main_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',      'oxdelivery', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxdelivery', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxdelivery', 1, 0, 0 ),
                                        array( 'oxid',         'oxdelivery', 0, 0, 1 )
                                        ),
                                    'container2' => array(
                                        array( 'oxtitle',      'oxdelivery', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxdelivery', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxdelivery', 1, 0, 0 ),
                                        array( 'oxid',  'oxdel2delset', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sId = $this->getConfig()->getRequestParameter( 'oxid' );
        $sSynchId = $this->getConfig()->getRequestParameter( 'synchoxid' );
        $oDb = oxDb::getDb();

        $sDeliveryViewName = $this->_getViewName('oxdelivery');

        // category selected or not ?
        if ( !$sId) {
            $sQAdd  = " from $sDeliveryViewName where 1 ";
        } else {
            $sQAdd  = " from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = ".$oDb->quote( $sId );
        }

        if ( $sSynchId && $sSynchId != $sId ) {
            $sQAdd .= "and $sDeliveryViewName.oxid not in ( select $sDeliveryViewName.oxid from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = ".$oDb->quote( $sSynchId ) ." ) ";
        }

        return $sQAdd;
    }

    /**
     * Remove this delivery cost from these sets
     *
     * @return null
     */
    public function removeFromSet()
    {
        $aRemoveGroups = $this->_getActionIds( 'oxdel2delset.oxid' );
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxdel2delset.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxdel2delset where oxdel2delset.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aRemoveGroups ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds this delivery cost to these sets
     *
     * @return null
     */
    public function addToSet()
    {
        $aChosenSets = $this->_getActionIds( 'oxdelivery.oxid' );
        $soxId       = oxConfig::getParameter( 'synchoxid');

        // adding
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sDeliveryViewName = $this->_getViewName('oxdelivery');
            $aChosenSets = $this->_getAll( $this->_addFilter( "select $sDeliveryViewName.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenSets ) ) {
            $oDb = oxDb::getDb();
            foreach ( $aChosenSets as $sChosenSet) {
                // check if we have this entry already in
                $sID = $oDb->getOne("select oxid from oxdel2delset where oxdelid =  " . $oDb->quote( $sChosenSet ) . " and oxdelsetid = ".$oDb->quote( $soxId ), false, false );
                if ( !isset( $sID) || !$sID) {
                    $oDel2delset = oxNew( 'oxbase' );
                    $oDel2delset->init( 'oxdel2delset' );
                    $oDel2delset->oxdel2delset__oxdelid    = new oxField($sChosenSet);
                    $oDel2delset->oxdel2delset__oxdelsetid = new oxField($soxId);
                    $oDel2delset->save();
                }
            }
        }
    }
}
