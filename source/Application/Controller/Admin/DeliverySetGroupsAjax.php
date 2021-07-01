<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages deliveryset groups
 */
class DeliverySetGroupsAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,  visible, multilanguage, ident
        ['oxtitle', 'oxgroups', 1, 0, 0],
        ['oxid', 'oxgroups', 0, 0, 0],
        ['oxid', 'oxgroups', 0, 0, 1],
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxgroups', 1, 0, 0],
                                     ['oxid', 'oxgroups', 0, 0, 0],
                                     ['oxid', 'oxobject2delivery', 0, 0, 1],
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        $sgroupTable = $this->getViewName('oxgroups');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from $sgroupTable where 1 ";
        } else {
            $sQAdd = " from oxobject2delivery, {$sgroupTable} " .
                     "where oxobject2delivery.oxdeliveryid = " . $oDb->quote($sId) .
                     " and oxobject2delivery.oxobjectid = {$sgroupTable}.oxid " .
                     "and oxobject2delivery.oxtype = 'oxdelsetg' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= " and {$sgroupTable}.oxid not in ( select {$sgroupTable}.oxid " .
                      "from oxobject2delivery, {$sgroupTable} " .
                      "where oxobject2delivery.oxdeliveryid = " . $oDb->quote($sSynchId) .
                      " and oxobject2delivery.oxobjectid = $sgroupTable.oxid " .
                      "and oxobject2delivery.oxtype = 'oxdelsetg' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user group from delivery sets config
     */
    public function removeGroupFromSet()
    {
        $aRemoveGroups = $this->getActionIds('oxobject2delivery.oxid');
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = $this->addFilter("delete oxobject2delivery.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sRemoveGroups = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups));
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . $sRemoveGroups . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds user group to delivery sets config
     */
    public function addGroupToSet()
    {
        $aChosenCat = $this->getActionIds('oxgroups.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // adding
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sGroupTable = $this->getViewName('oxgroups');
            $aChosenCat = $this->getAll($this->addFilter("select $sGroupTable.oxid " . $this->getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aChosenCat)) {
            foreach ($aChosenCat as $sChosenCat) {
                $oObject2Delivery = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Delivery->init('oxobject2delivery');
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenCat);
                $oObject2Delivery->oxobject2delivery__oxtype = new \OxidEsales\Eshop\Core\Field("oxdelsetg");
                $oObject2Delivery->save();
            }
        }
    }
}
