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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use stdClass;

/**
 * Admin article RDFa deliveryset manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Shipping & Handling -> RDFa.
 */
class DeliverySetRdfa extends \payment_rdfa
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
    protected $_aRDFaDeliveries = array(
        "DeliveryModeDirectDownload" => 0,
        "DeliveryModeFreight"        => 0,
        "DeliveryModeMail"           => 0,
        "DeliveryModeOwnFleet"       => 0,
        "DeliveryModePickUp"         => 0,
        "DHL"                        => 1,
        "FederalExpress"             => 1,
        "UPS"                        => 1
    );

    /**
     * Saves changed mapping configurations
     */
    public function save()
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        $aRDFaDeliveries = (array) oxRegistry::getConfig()->getRequestParameter("ardfadeliveries");

        // Delete old mappings
        $oDb = oxDb::getDb();
        $sOxIdParameter = oxRegistry::getConfig()->getRequestParameter("oxid");
        $sSql = "DELETE FROM oxobject2delivery WHERE oxdeliveryid = '{$sOxIdParameter}' AND OXTYPE = 'rdfadeliveryset'";
        $oDb->execute($sSql);

        // Save new mappings
        foreach ($aRDFaDeliveries as $sDelivery) {
            $oMapping = oxNew("oxBase");
            $oMapping->init("oxobject2delivery");
            $oMapping->assign($aParams);
            $oMapping->oxobject2delivery__oxobjectid = new oxField($sDelivery);
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
        $aRDFaDeliveries = array();
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
        $oDb = oxDb::getDb();
        $aRDFaDeliveries = array();
        $sSelect = 'select oxobjectid from oxobject2delivery where oxdeliveryid=' . $oDb->quote(oxRegistry::getConfig()->getRequestParameter("oxid")) . ' and oxtype = "rdfadeliveryset" ';
        $rs = $oDb->select($sSelect);
        if ($rs && $rs->count()) {
            while (!$rs->EOF) {
                $aRDFaDeliveries[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        return $aRDFaDeliveries;
    }
}
