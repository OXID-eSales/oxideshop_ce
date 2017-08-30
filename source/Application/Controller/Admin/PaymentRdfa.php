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

use oxRegistry;
use oxDb;
use stdClass;
use oxField;

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
     * 0 value have general payments, 1 have creditcar payments
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
                                      "PaySwarm"                => 0,
                                      "AmericanExpress"         => 1,
                                      "DinersClub"              => 1,
                                      "Discover"                => 1,
                                      "JCB"                     => 1,
                                      "MasterCard"              => 1,
                                      "VISA"                    => 1];

    /**
     * Saves changed mapping configurations
     */
    public function save()
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        $aRDFaPayments = (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("ardfapayments");

        // Delete old mappings
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oDb->execute("DELETE FROM oxobject2payment WHERE oxpaymentid = '" . \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid") . "' AND OXTYPE = 'rdfapayment'");

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
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $aRDFaPayments = [];
        $sSelect = 'select oxobjectid from oxobject2payment where oxpaymentid=' . $oDb->quote(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid")) . ' and oxtype = "rdfapayment" ';
        $rs = $oDb->select($sSelect);
        if ($rs && $rs->count()) {
            while (!$rs->EOF) {
                $aRDFaPayments[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aRDFaPayments;
    }
}
