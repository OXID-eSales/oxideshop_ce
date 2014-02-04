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
 * Class manages payment countries
 */
class payment_country_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */    
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid',        'oxcountry', 0, 0, 1 )
                                        ),
                                    'container2' => array(
                                        array( 'oxtitle',     'oxcountry', 1, 1, 0 ),
                                        array( 'oxisoalpha2', 'oxcountry', 1, 0, 0 ),
                                        array( 'oxisoalpha3', 'oxcountry', 0, 0, 0 ),
                                        array( 'oxunnum3',    'oxcountry', 0, 0, 0 ),
                                        array( 'oxid', 'oxobject2payment', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        // looking for table/view
        $sCountryTable = $this->_getViewName('oxcountry');
        $oDb = oxDb::getDb();
        $sCountryId = $this->getConfig()->getRequestParameter( 'oxid' );
        $sSynchCountryId = $this->getConfig()->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sCountryId) {
            // which fields to load ?
            $sQAdd = " from $sCountryTable where $sCountryTable.oxactive = '1' ";
        } else {

            $sQAdd  = " from oxobject2payment left join $sCountryTable on $sCountryTable.oxid=oxobject2payment.oxobjectid ";
            $sQAdd .= "where $sCountryTable.oxactive = '1' and oxobject2payment.oxpaymentid = ".$oDb->quote( $sCountryId )." and oxobject2payment.oxtype = 'oxcountry' ";
        }

        if ( $sSynchCountryId && $sSynchCountryId != $sCountryId ) {
            $sQAdd .= "and $sCountryTable.oxid not in ( ";
            $sQAdd .= "select $sCountryTable.oxid from oxobject2payment left join $sCountryTable on $sCountryTable.oxid=oxobject2payment.oxobjectid ";
            $sQAdd .= "where oxobject2payment.oxpaymentid = ".$oDb->quote( $sSynchCountryId )." and oxobject2payment.oxtype = 'oxcountry' ) ";
        }

        return $sQAdd;
    }

    /**
     * Adds chosen user group (groups) to delivery list
     *
     * @return null
     */
    public function addPayCountry()
    {
        $aChosenCntr = $this->_getActionIds( 'oxcountry.oxid' );
        $soxId       = $this->getConfig()->getRequestParameter( 'synchoxid');

        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sCountryTable = $this->_getViewName('oxcountry');
            $aChosenCntr = $this->_getAll( $this->_addFilter( "select $sCountryTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew( 'oxbase' );
                $oObject2Payment->init( 'oxobject2payment' );
                $oObject2Payment->oxobject2payment__oxpaymentid = new oxField($soxId);
                $oObject2Payment->oxobject2payment__oxobjectid  = new oxField($sChosenCntr);
                $oObject2Payment->oxobject2payment__oxtype      = new oxField("oxcountry");
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen user group (groups) from delivery list
     *
     * @return null
     */
    public function removePayCountry()
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
}
