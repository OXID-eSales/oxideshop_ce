<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages promotion groups
 */
class ActionsGroupsAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = [
        // field , table,  visible, multilanguage, ident
        'container1' => [
            ['oxtitle', 'oxgroups', 1, 0, 0],
            ['oxid', 'oxgroups', 0, 0, 0],
            ['oxid', 'oxgroups', 0, 0, 1],
        ],
         'container2' => [
             ['oxtitle', 'oxgroups', 1, 0, 0],
             ['oxid', 'oxgroups', 0, 0, 0],
             ['oxid', 'oxobject2action', 0, 0, 1],
         ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery()
    {
        // active AJAX component
        $sGroupTable = $this->getViewName('oxgroups');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from {$sGroupTable} where 1 ";
        } else {
            $sQAdd = " from oxobject2action, {$sGroupTable} where {$sGroupTable}.oxid=oxobject2action.oxobjectid " .
                      " and oxobject2action.oxactionid = " . $oDb->quote($sId) .
                      " and oxobject2action.oxclass = 'oxgroups' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= " and {$sGroupTable}.oxid not in ( select {$sGroupTable}.oxid " .
                      "from oxobject2action, {$sGroupTable} where $sGroupTable.oxid=oxobject2action.oxobjectid " .
                      " and oxobject2action.oxactionid = " . $oDb->quote($sSynchId) .
                      " and oxobject2action.oxclass = 'oxgroups' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user group from promotion
     */
    public function removePromotionGroup()
    {
        $aRemoveGroups = $this->getActionIds('oxobject2action.oxid');
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = $this->addFilter("delete oxobject2action.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sRemoveGroups = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups));
            $sQ = "delete from oxobject2action where oxobject2action.oxid in (" . $sRemoveGroups . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds user group to promotion
     *
     * @return bool Whether at least one promotion was added.
     */
    public function addPromotionGroup()
    {
        $aChosenGroup = $this->getActionIds('oxgroups.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sGroupTable = $this->getViewName('oxgroups');
            $aChosenGroup = $this->getAll($this->addFilter("select $sGroupTable.oxid " . $this->getQuery()));
        }

        $promotionAdded = false;
        if ($soxId && $soxId != "-1" && is_array($aChosenGroup)) {
            foreach ($aChosenGroup as $sChosenGroup) {
                $oObject2Promotion = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Promotion->init('oxobject2action');
                $oObject2Promotion->oxobject2action__oxactionid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oObject2Promotion->oxobject2action__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenGroup);
                $oObject2Promotion->oxobject2action__oxclass = new \OxidEsales\Eshop\Core\Field("oxgroups");
                $oObject2Promotion->save();
            }

            $promotionAdded = true;
        }

        return $promotionAdded;
    }
}
