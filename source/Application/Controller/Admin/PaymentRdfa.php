<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use stdClass;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article RDFa payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> RDFa.
 */
class PaymentRdfa extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "payment_rdfa.tpl";

    /**
     * Predefined RDFa payment methods
     * 0 value have general payments
     *
     * @var array
     */
    protected $_aRDFaPayments = ["ByBankTransferInAdvance" => 0,
                                      "ByInvoice"               => 0,
                                      "Cash"                    => 0,
                                      "CheckInAdvance"          => 0,
                                      "COD"                     => 0,
                                      "DirectDebit"             => 0,
                                      "GoogleCheckout"          => 0,
                                      "PayPal"                  => 0,
                                      "PaySwarm"                => 0];

    /**
     * Saves changed mapping configurations
     */
    public function save()
    {
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
        $aRDFaPayments = (array) Registry::getRequest()->getRequestEscapedParameter("ardfapayments");

        // Delete old mappings
        $oDb = DatabaseProvider::getDb();
        $oDb->execute("DELETE FROM oxobject2payment WHERE oxpaymentid = :oxpaymentid AND OXTYPE = 'rdfapayment'", [
            ':oxpaymentid' => Registry::getRequest()->getRequestEscapedParameter("oxid")
        ]);

        // Save new mappings
        foreach ($aRDFaPayments as $sPayment) {
            $oMapping = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
            $oMapping->init("oxobject2payment");
            $oMapping->assign($aParams);
            $oMapping->oxobject2payment__oxobjectid = new \OxidEsales\Eshop\Core\Field($sPayment);
            $oMapping->save();
        }
    }

    /**
     * Returns an array including all available RDFa payments.
     *
     * @return array
     */
    public function getAllRDFaPayments()
    {
        $aRDFaPayments = [];
        $aAssignedRDFaPayments = $this->getAssignedRDFaPayments();
        foreach ($this->_aRDFaPayments as $sName => $iType) {
            $oPayment = new stdClass();
            $oPayment->name = $sName;
            $oPayment->type = $iType;
            $oPayment->checked = in_array($sName, $aAssignedRDFaPayments);
            $aRDFaPayments[] = $oPayment;
        }

        return $aRDFaPayments;
    }

    /**
     * Returns array of RDFa payments which are assigned to current payment
     *
     * @return array
     */
    public function getAssignedRDFaPayments()
    {
        $oDb = DatabaseProvider::getDb();
        $aRDFaPayments = [];
        $sSelect = 'select oxobjectid from oxobject2payment where oxpaymentid = :oxpaymentid and oxtype = "rdfapayment" ';
        $rs = $oDb->select($sSelect, [
            ':oxpaymentid' => Registry::getRequest()->getRequestEscapedParameter("oxid")
        ]);
        if ($rs && $rs->count()) {
            while (!$rs->EOF) {
                $aRDFaPayments[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aRDFaPayments;
    }
}
