<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
class OrderMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Whitelist of parameters whose change does not require a full order recalculation.
     *
     * @var array
     */
    protected $fieldsTriggerNoOrderRecalculation = ['oxorder__oxordernr',
                                                         'oxorder__oxbillnr',
                                                         'oxorder__oxtrackcode',
                                                         'oxorder__oxpaid'];

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
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrder->load($soxId);

            // paid ?
            $sOxPaidField = 'oxorder__oxpaid';
            $sDelTypeField = 'oxorder__oxdeltype';

            if ($oOrder->$sOxPaidField->value != "0000-00-00 00:00:00") {
                $oOrder->blIsPaid = true;
                /** @var \OxidEsales\Eshop\Core\UtilsDate $oUtilsDate */
                $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
                $oOrder->$sOxPaidField = new \OxidEsales\Eshop\Core\Field($oUtilsDate->formatDBDate($oOrder->$sOxPaidField->value));
            }

            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["paymentType"] = $oOrder->getPaymentType();
            $this->_aViewData["oShipSet"] = $oOrder->getShippingSetList();

            if ($oOrder->$sDelTypeField->value) {
                // order user
                $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                $oUser->load($oOrder->oxorder__oxuserid->value);

                // order sum in default currency
                $dPrice = $oOrder->oxorder__oxtotalbrutsum->value / $oOrder->oxorder__oxcurrate->value;

                /** @var \OxidEsales\Eshop\Application\Model\PaymentList $oPaymentList */
                $oPaymentList = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\PaymentList::class);
                $this->_aViewData["oPayments"] =
                                        $oPaymentList->getPaymentList($oOrder->$sDelTypeField->value, $dPrice, $oUser);
            }

            // any voucher used ?
            $this->_aViewData["aVouchers"] = $oOrder->getVoucherNrList();
        }

        $this->_aViewData["sNowValue"] = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

        return "order_main.tpl";
    }

    /**
     * Saves main orders configuration parameters.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
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
                if (($value != $orderField->value) && !in_array($parameter, $this->fieldsTriggerNoOrderRecalculation)) {
                    $needOrderRecalculate = true;
                    continue;
                }
            }
        }

        //change payment
        $sPayId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("setPayment");
        if (!empty($sPayId) && ($sPayId != $oOrder->oxorder__oxpaymenttype->value)) {
            $aParams['oxorder__oxpaymenttype'] = $sPayId;
            $needOrderRecalculate = true;
        }

        $oOrder->assign($aParams);

        $aDynvalues = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("dynvalue");
        if (isset($aDynvalues)) {
            $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
            $oPayment->load($oOrder->oxorder__oxpaymentid->value);
            $oPayment->oxuserpayments__oxvalue->setValue(\OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesToText($aDynvalues));
            $oPayment->save();
            $needOrderRecalculate = true;
        }
        //change delivery set
        $sDelSetId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("setDelSet");
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
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($soxId)) {
            // #632A
            $oOrder->oxorder__oxsenddate = new \OxidEsales\Eshop\Core\Field(date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
            $oOrder->save();

            // #1071C
            $oOrder->getOrderArticles(true);
            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sendmail")) {
                // send eMail
                $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
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
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($soxId)) {
            $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
            $oEmail->sendDownloadLinksMail($oOrder);
        }
    }

    /**
     * Resets order shipping date.
     */
    public function resetOrder()
    {
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($this->getEditObjectId())) {
            $oOrder->oxorder__oxsenddate = new \OxidEsales\Eshop\Core\Field("0000-00-00 00:00:00");
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
