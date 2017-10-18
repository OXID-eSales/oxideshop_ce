<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxDb;
use oxField;

/**
 * Class manages delivery categories
 */
class DeliveryCategoriesAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxcategories', 1, 1, 0],
        ['oxdesc', 'oxcategories', 1, 1, 0],
        ['oxid', 'oxcategories', 0, 0, 0],
        ['oxid', 'oxcategories', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxcategories', 1, 1, 0],
                                     ['oxdesc', 'oxcategories', 1, 1, 0],
                                     ['oxid', 'oxcategories', 0, 0, 0],
                                     ['oxid', 'oxobject2delivery', 0, 0, 1],
                                     ['oxid', 'oxcategories', 0, 0, 1]
                                 ],
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        // looking for table/view
        $sCatTable = $this->_getViewName('oxcategories');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelId = $this->getConfig()->getRequestParameter('oxid');
        $sSynchDelId = $this->getConfig()->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sDelId) {
            $sQAdd = " from {$sCatTable} ";
        } else {
            $sQAdd = " from oxobject2delivery left join {$sCatTable} " .
                     "on {$sCatTable}.oxid=oxobject2delivery.oxobjectid " .
                     " where oxobject2delivery.oxdeliveryid = " . $oDb->quote($sDelId) .
                     " and oxobject2delivery.oxtype = 'oxcategories' ";
        }

        if ($sSynchDelId && $sSynchDelId != $sDelId) {
            // performance
            $sSubSelect = " select {$sCatTable}.oxid from oxobject2delivery left join {$sCatTable} " .
                          "on {$sCatTable}.oxid=oxobject2delivery.oxobjectid " .
                          " where oxobject2delivery.oxdeliveryid = " . $oDb->quote($sSynchDelId) .
                          " and oxobject2delivery.oxtype = 'oxcategories' ";
            if (stristr($sQAdd, 'where') === false) {
                $sQAdd .= ' where ';
            } else {
                $sQAdd .= ' and ';
            }
            $sQAdd .= " {$sCatTable}.oxid not in ( $sSubSelect ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes category from delivery configuration
     */
    public function removeCatFromDel()
    {
        $aChosenCat = $this->_getActionIds('oxobject2delivery.oxid');

        // removing all
        if ($this->getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxobject2delivery.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenCat)) {
            $sChosenCategoriess = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenCat));
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . $sChosenCategoriess . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds category to delivery configuration
     */
    public function addCatToDel()
    {
        $aChosenCat = $this->_getActionIds('oxcategories.oxid');
        $soxId = $this->getConfig()->getRequestParameter('synchoxid');

        // adding
        if ($this->getConfig()->getRequestParameter('all')) {
            $sCatTable = $this->_getViewName('oxcategories');
            $aChosenCat = $this->_getAll($this->_addFilter("select $sCatTable.oxid " . $this->_getQuery()));
        }

        if (isset($soxId) && $soxId != "-1" && isset($aChosenCat) && $aChosenCat) {
            foreach ($aChosenCat as $sChosenCat) {
                $oObject2Delivery = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Delivery->init('oxobject2delivery');
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenCat);
                $oObject2Delivery->oxobject2delivery__oxtype = new \OxidEsales\Eshop\Core\Field("oxcategories");
                $oObject2Delivery->save();
            }
        }
    }
}
