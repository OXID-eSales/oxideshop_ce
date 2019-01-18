<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin deliveryset payment manager.
 * There is possibility to assign set to payment method
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling Set -> Payment
 */
class DeliverySetPayment extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $odeliveryset = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);
            $odeliveryset->setLanguage($this->_iEditLang);
            $odeliveryset->load($soxId);

            $oOtherLang = $odeliveryset->getAvailableInLangs();

            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $odeliveryset->setLanguage(key($oOtherLang));
                $odeliveryset->load($soxId);
            }

            $this->_aViewData["edit"] = $odeliveryset;

            //Disable editing for derived articles
            if ($odeliveryset->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oDeliverysetPaymentAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetPaymentAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetPaymentAjax->getColumns();

            return "popups/deliveryset_payment.tpl";
        } elseif ($iAoc == 2) {
            $oDeliverysetCountryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetCountryAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetCountryAjax->getColumns();

            return "popups/deliveryset_country.tpl";
        }

        return "deliveryset_payment.tpl";
    }
}
