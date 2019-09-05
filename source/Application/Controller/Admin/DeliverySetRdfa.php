<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use oxRegistry;
use oxDb;
use oxField;
use stdClass;

/**
 * Admin article RDFa deliveryset manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Shipping & Handling -> RDFa.
 */
class DeliverySetRdfa extends \OxidEsales\Eshop\Application\Controller\Admin\PaymentRdfa
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "deliveryset_rdfa.tpl";

    /**
     * Predefined delivery methods
     *
     * @var array
     */
    protected $_aRDFaDeliveries = [
        "DeliveryModeDirectDownload" => 0,
        "DeliveryModeFreight"        => 0,
        "DeliveryModeMail"           => 0,
        "DeliveryModeOwnFleet"       => 0,
        "DeliveryModePickUp"         => 0,
        "DHL"                        => 1,
        "FederalExpress"             => 1,
        "UPS"                        => 1
    ];

    /**
     * Saves changed mapping configurations
     */
    public function save()
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        $aRDFaDeliveries = (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("ardfadeliveries");

        // Delete old mappings
        $oDb = DatabaseProvider::getDb();
        $sOxIdParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid");
        $sSql = "DELETE FROM oxobject2delivery WHERE oxdeliveryid = :oxdeliveryid AND OXTYPE = 'rdfadeliveryset'";
        $oDb->execute($sSql, [
            ':oxdeliveryid' => $sOxIdParameter
        ]);

        // Save new mappings
        foreach ($aRDFaDeliveries as $sDelivery) {
            $oMapping = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
            $oMapping->init("oxobject2delivery");
            $oMapping->assign($aParams);
            $oMapping->oxobject2delivery__oxobjectid = new \OxidEsales\Eshop\Core\Field($sDelivery);
            $oMapping->save();
        }
    }

    /**
     * Returns an array including all available RDFa deliveries.
     *
     * @return array
     */
    public function getAllRDFaDeliveries()
    {
        $aRDFaDeliveries = [];
        $aAssignedRDFaDeliveries = $this->getAssignedRDFaDeliveries();
        foreach ($this->_aRDFaDeliveries as $sName => $iType) {
            $oDelivery = new stdClass();
            $oDelivery->name = $sName;
            $oDelivery->type = $iType;
            $oDelivery->checked = in_array($sName, $aAssignedRDFaDeliveries);
            $aRDFaDeliveries[] = $oDelivery;
        }

        return $aRDFaDeliveries;
    }

    /**
     * Returns array of RDFa deliveries which are assigned to current delivery
     *
     * @return array
     */
    public function getAssignedRDFaDeliveries()
    {
        $oDb = DatabaseProvider::getDb();
        $aRDFaDeliveries = [];
        $sSelect = 'select oxobjectid from oxobject2delivery where oxdeliveryid = :oxdeliveryid and oxtype = "rdfadeliveryset" ';
        $rs = $oDb->select($sSelect, [
            ':oxdeliveryid' => \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid")
        ]);
        if ($rs && $rs->count()) {
            while (!$rs->EOF) {
                $aRDFaDeliveries[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aRDFaDeliveries;
    }
}
