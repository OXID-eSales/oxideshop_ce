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
 * Admin article main payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> Main.
 * @package admin
 */
class Payment_Country extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxlist object,
     * passes it's data to Smarty engine and retutns name of template
     * file "payment_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        // remove itm from list
        unset( $this->_aViewData["sumtype"][2]);

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oPayment = oxNew( "oxpayment" );
            $oPayment->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oPayment->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oPayment->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oPayment;

            // remove already created languages
            $aLang = array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang );
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if ( oxConfig::getParameter("aoc") ) {
            $oPaymentCountryAjax = oxNew( 'payment_country_ajax' );
            $this->_aViewData['oxajax'] = $oPaymentCountryAjax->getColumns();

            return "popups/payment_country.tpl";
        }
        return "payment_country.tpl";
    }

    /**
     * Adds chosen user group (groups) to delivery list
     *
     * @return null
     */
    public function addcountry()
    {
        $sOxId = $this->getEditObjectId();
        $aChosenCntr = oxConfig::getParameter( "allcountries" );
        if ( isset( $sOxId ) && $sOxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr ) {
                $oObject2Payment = oxNew( 'oxbase' );
                $oObject2Payment->init( 'oxobject2payment' );
                $oObject2Payment->oxobject2payment__oxpaymentid = new oxField( $sOxId );
                $oObject2Payment->oxobject2payment__oxobjectid  = new oxField( $sChosenCntr );
                $oObject2Payment->oxobject2payment__oxtype      = new oxField( "oxcountry" );
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen user group (groups) from delivery list
     *
     * @return null
     */
    public function removecountry()
    {
        $sOxId = $this->getEditObjectId();
        $aChosenCntr = oxConfig::getParameter( "countries" );
        if ( isset( $sOxId ) && $sOxId != "-1" && is_array( $aChosenCntr ) ) {
            foreach ( $aChosenCntr as $sChosenCntr ) {
                $oObject2Payment = oxNew( 'oxbase' );
                $oObject2Payment->init( 'oxobject2payment' );
                $oObject2Payment->delete( $sChosenCntr );
            }
        }
    }
}
