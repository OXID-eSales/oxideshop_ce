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
 * Class manages deliveryset payment
 */
class deliveryset_payment_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxdesc',       'oxpayments', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxpayments', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxpayments', 0, 0, 0 ),
                                        array( 'oxid',         'oxpayments', 0, 0, 1 )
                                        ),
                                    'container2' => array(
                                        array( 'oxdesc',       'oxpayments', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxpayments', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxpayments', 0, 0, 0 ),
                                        array( 'oxid',  'oxobject2payment', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oDb = oxDb::getDb();
        $sId = $this->getConfig()->getRequestParameter( 'oxid' );
        $sSynchId = $this->getConfig()->getRequestParameter( 'synchoxid' );

        $sPayTable = $this->_getViewName('oxpayments');

        // category selected or not ?
        if ( !$sId) {
            $sQAdd = " from $sPayTable where 1 ";
        } else {
            $sQAdd  = " from oxobject2payment, $sPayTable where oxobject2payment.oxobjectid = ".$oDb->quote( $sId );
            $sQAdd .= " and oxobject2payment.oxpaymentid = $sPayTable.oxid and oxobject2payment.oxtype = 'oxdelset' ";
        }

        if ( $sSynchId && $sSynchId != $sId) {
            $sQAdd .= "and $sPayTable.oxid not in ( select $sPayTable.oxid from oxobject2payment, $sPayTable where oxobject2payment.oxobjectid = ".$oDb->quote( $sSynchId );
            $sQAdd .= "and oxobject2payment.oxpaymentid = $sPayTable.oxid and oxobject2payment.oxtype = 'oxdelset' ) ";
        }

        return $sQAdd;
    }

    /**
     * Remove these payments from this set
     *
     * @return null
     */
    public function removePayFromSet()
    {
        $aChosenCntr = $this->_getActionIds( 'oxobject2payment.oxid' );
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2payment.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenCntr ) ) {
            $sQ = "delete from oxobject2payment where oxobject2payment.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenCntr ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

     /**
     * Adds this payments to this set
     *
     * @return null
     */
    public function addPayToSet()
    {
        $aChosenSets = $this->_getActionIds( 'oxpayments.oxid' );
        $soxId       = $this->getConfig()->getRequestParameter( 'synchoxid');

        // adding
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sPayTable = $this->_getViewName('oxpayments');
            $aChosenSets = $this->_getAll( $this->_addFilter( "select $sPayTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenSets ) ) {
            $oDb = oxDb::getDb();
            foreach ( $aChosenSets as $sChosenSet) {
                // check if we have this entry already in
                $sID = $oDb->getOne("select oxid from oxobject2payment where oxpaymentid = " . $oDb->quote( $sChosenSet ) . "  and oxobjectid = ".$oDb->quote( $soxId )." and oxtype = 'oxdelset'", false, false);
                if ( !isset( $sID) || !$sID) {
                    $oObject = oxNew( 'oxbase' );
                    $oObject->init( 'oxobject2payment' );
                    $oObject->oxobject2payment__oxpaymentid = new oxField($sChosenSet);
                    $oObject->oxobject2payment__oxobjectid  = new oxField($soxId);
                    $oObject->oxobject2payment__oxtype      = new oxField("oxdelset");
                    $oObject->save();
                }
            }
        }
    }
}
