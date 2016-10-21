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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxUtilsDate;
use oxRegistry;
use oxField;
use oxPaymentList;

/**
 * Admin article main order manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Orders -> Display Orders -> Main.
 */
class OrderMain extends \oxAdminDetails
{
    /**
     * Whitelist of parameters whose change does not require a full order recalculation.
     *
     * @var array
     */
    protected $fieldsTriggerNoOrderRecalculation = array('oxorder__oxordernr',
                                                         'oxorder__oxbillnr',
                                                         'oxorder__oxtrackcode',
                                                         'oxorder__oxpaid');

    /**
     * Executes parent method parent::render(), creates oxorder and
     * oxuserpayment objects, passes data to Smarty engine and returns
     * name of template file "order_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oOrder = oxNew("oxorder");
            $oOrder->load($soxId);

            // paid ?
            $sOxPaidField = 'oxorder__oxpaid';
            $sDelTypeField = 'oxorder__oxdeltype';

            if ($oOrder->$sOxPaidField->value != "0000-00-00 00:00:00") {
                $oOrder->blIsPaid = true;
                /** @var oxUtilsDate $oUtilsDate */
                $oUtilsDate = oxRegistry::get("oxUtilsDate");
                $oOrder->$sOxPaidField = new oxField($oUtilsDate->formatDBDate($oOrder->$sOxPaidField->value));
            }

            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["paymentType"] = $oOrder->getPaymentType();
            $this->_aViewData["oShipSet"] = $oOrder->getShippingSetList();

            if ($oOrder->$sDelTypeField->value) {
                // order user
                $oUser = oxNew('oxuser');
                $oUser->load($oOrder->oxorder__oxuserid->value);

                // order sum in default currency
                $dPrice = $oOrder->oxorder__oxtotalbrutsum->value / $oOrder->oxorder__oxcurrate->value;

                /** @var oxPaymentList $oPaymentList */
                $oPaymentList = oxRegistry::get("oxPaymentList");
                $this->_aViewData["oPayments"] =
                                        $oPaymentList->getPaymentList($oOrder->$sDelTypeField->value, $dPrice, $oUser);
            }

            // any voucher used ?
            $this->_aViewData["aVouchers"] = $oOrder->getVoucherNrList();
        }

        $this->_aViewData["sNowValue"] = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime());

        return "order_main.tpl";
    }

    /**
     * Saves main orders configuration parameters.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oOrder = oxNew("oxorder");
        if ($soxId != "-1") {
            $oOrder->load($soxId);
        } else {
            $aParams['oxorder__oxid'] = null;
        }

        $needOrderRecalculate = false;
        if (is_array($aParams)) {
            foreach ($aParams as $parameter => $value) {
                //parameter changes for not whitelisted parameters trigger order recalculation
                $orderField = $oOrder->$parameter;
                if ( ($value != $orderField->value) && !in_array($parameter, $this->fieldsTriggerNoOrderRecalculation) ) {
                    $needOrderRecalculate = true;
                    continue;
                }
            }
        }

        //change payment
        $sPayId = oxRegistry::getConfig()->getRequestParameter("setPayment");
        if (!empty($sPayId) && ($sPayId != $oOrder->oxorder__oxpaymenttype->value)) {
            $aParams['oxorder__oxpaymenttype'] = $sPayId;
            $needOrderRecalculate = true;
        }

        $oOrder->assign($aParams);

        $aDynvalues = oxRegistry::getConfig()->getRequestParameter("dynvalue");
        if (isset($aDynvalues)) {
            $oPayment = oxNew("oxuserpayment");
            $oPayment->load($oOrder->oxorder__oxpaymentid->value);
            $oPayment->oxuserpayments__oxvalue->setValue(oxRegistry::getUtils()->assignValuesToText($aDynvalues));
            $oPayment->save();
            $needOrderRecalculate = true;
        }
        //change delivery set
        $sDelSetId = oxRegistry::getConfig()->getRequestParameter("setDelSet");
        if (!empty($sDelSetId) && ($sDelSetId != $oOrder->oxorder__oxdeltype->value)) {
            $oOrder->oxorder__oxpaymenttype->setValue("oxempty");
            $oOrder->setDelivery($sDelSetId);
            $needOrderRecalculate = true;
        } else {
            // keeps old delivery cost
            $oOrder->reloadDelivery(false);
        }

        if ($needOrderRecalculate) {
            // keeps old discount
            $oOrder->reloadDiscount(false);
            $oOrder->recalculateOrder();
        } else {
            //nothing changed in order that requires a full recalculation
            $oOrder->save();
        }

        // set oxid if inserted
        $this->setEditObjectId($oOrder->getId());
    }

    /**
     * Sends order.
     */
    public function sendOrder()
    {
        $soxId = $this->getEditObjectId();
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($soxId)) {

            // #632A
            $oOrder->oxorder__oxsenddate = new oxField(date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime()));
            $oOrder->save();

            // #1071C
            $oOrderArticles = $oOrder->getOrderArticles(true);
            if (oxRegistry::getConfig()->getRequestParameter("sendmail")) {
                // send eMail
                $oEmail = oxNew("oxemail");
                $oEmail->sendSendedNowMail($oOrder);
            }
            $this->onOrderSend();
        }
    }

    /**
     * Sends download links.
     */
    public function sendDownloadLinks()
    {
        $soxId = $this->getEditObjectId();
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($soxId)) {
            $oEmail = oxNew("oxemail");
            $oEmail->sendDownloadLinksMail($oOrder);
        }
    }

    /**
     * Resets order shipping date.
     */
    public function resetOrder()
    {
        $oOrder = oxNew("oxorder");
        if ($oOrder->load($this->getEditObjectId())) {

            $oOrder->oxorder__oxsenddate = new oxField("0000-00-00 00:00:00");
            $oOrder->save();

            $this->onOrderReset();
        }
    }

    /**
     * Method is used for overriding.
     */
    protected function onOrderSend()
    {
    }

    /**
     * Method is used for overriding.
     */
    protected function onOrderReset()
    {
    }
}
