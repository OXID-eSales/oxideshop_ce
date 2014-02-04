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
 * Class manages discount countries
 */
class discount_main_ajax extends ajaxListComponent
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
                                        array( 'oxid', 'oxobject2discount', 0, 0, 1 )
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
        $sCountryTable = $this->_getViewName('oxcountry');
        $oDb = oxDb::getDb();
        $sId = $oConfig->getRequestParameter( 'oxid' );
        $sSynchId = $oConfig->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sId) {
            $sQAdd  = " from $sCountryTable where $sCountryTable.oxactive = '1' ";
        } else {
            $sQAdd  = " from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid ";
            $sQAdd .= "and oxobject2discount.oxdiscountid = ".$oDb->quote( $sId )." and oxobject2discount.oxtype = 'oxcountry' ";
        }

        if ( $sSynchId && $sSynchId != $sId) {
            $sQAdd .= "and $sCountryTable.oxid not in ( select $sCountryTable.oxid from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid ";
            $sQAdd .= "and oxobject2discount.oxdiscountid = ".$oDb->quote( $sSynchId )." and oxobject2discount.oxtype = 'oxcountry' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes chosen user group (groups) from delivery list
     *
     * @return null
     */
    public function removeDiscCountry()
    {
        $oConfig = $this->getConfig();

        $aChosenCntr = $this->_getActionIds( 'oxobject2discount.oxid' );
        if ( $oConfig->getRequestParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2discount.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenCntr ) ) {
            $sQ = "delete from oxobject2discount where oxobject2discount.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenCntr ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds chosen user group (groups) to delivery list
     *
     * @return null
     */
    public function addDiscCountry()
    {
        $oConfig = $this->getConfig();
        $aChosenCntr = $this->_getActionIds( 'oxcountry.oxid' );
        $soxId       = $oConfig->getRequestParameter( 'synchoxid');


        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sCountryTable = $this->_getViewName('oxcountry');
            $aChosenCntr = $this->_getAll( $this->_addFilter( "select $sCountryTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr) {
                $oObject2Discount = oxNew( "oxbase" );
                $oObject2Discount->init( 'oxobject2discount' );
                $oObject2Discount->oxobject2discount__oxdiscountid = new oxField($soxId);
                $oObject2Discount->oxobject2discount__oxobjectid   = new oxField($sChosenCntr);
                $oObject2Discount->oxobject2discount__oxtype       = new oxField("oxcountry");
                $oObject2Discount->save();
            }
        }
    }
}
