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
 * Admin deliveryset payment manager.
 * There is possibility to assign set to payment method
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling Set -> Payment
 * @package admin
 */
class DeliverySet_Payment extends oxAdminDetails
{
    /**
     * Executes parent method parent::render()
     * passes data to Smarty engine and returns name of template file "deliveryset_payment.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $odeliveryset = oxNew( "oxdeliveryset" );
            $odeliveryset->setLanguage($this->_iEditLang);
            $odeliveryset->load( $soxId);

            $oOtherLang = $odeliveryset->getAvailableInLangs();

            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $odeliveryset->setLanguage(key($oOtherLang));
                $odeliveryset->load( $soxId );
            }

            $this->_aViewData["edit"] =  $odeliveryset;

            //Disable editing for derived articles
            if ($odeliveryset->isDerived())
                $this->_aViewData['readonly'] = true;
        }

        $iAoc = oxConfig::getParameter("aoc");
        if ( $iAoc == 1 ) {            
            $oDeliverysetPaymentAjax = oxNew( 'deliveryset_payment_ajax' );
            $this->_aViewData['oxajax'] = $oDeliverysetPaymentAjax->getColumns();

            return "popups/deliveryset_payment.tpl";
        } elseif ( $iAoc == 2 ) {
            $oDeliverysetCountryAjax = oxNew( 'deliveryset_country_ajax' );
            $this->_aViewData['oxajax'] = $oDeliverysetCountryAjax->getColumns();

            return "popups/deliveryset_country.tpl";
        }
        return "deliveryset_payment.tpl";
    }
}
