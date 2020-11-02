<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Class manages newsletter user groups rights.
 */
class NewsletterSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array.
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
            ['oxid', 'oxobject2group', 0, 0, 1],
        ],
    ];

    /**
     * Returns SQL query for data to fetc.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getQuery" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getQuery()
    {
        // active AJAX component
        $sGroupTable = $this->_getViewName('oxgroups');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDiscountId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchDiscountId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sDiscountId) {
            $sQAdd = " from $sGroupTable where 1 ";
        } else {
            $sQAdd = " from oxobject2group left join $sGroupTable on oxobject2group.oxgroupsid=$sGroupTable.oxid ";
            $sQAdd .= ' where oxobject2group.oxobjectid = ' . $oDb->quote($sDiscountId);
        }

        if ($sSynchDiscountId && $sSynchDiscountId !== $sDiscountId) {
            $sQAdd .= " and $sGroupTable.oxid not in ( ";
            $sQAdd .= " select $sGroupTable.oxid from oxobject2group left join $sGroupTable on oxobject2group.oxgroupsid=$sGroupTable.oxid ";
            $sQAdd .= ' where oxobject2group.oxobjectid = ' . $oDb->quote($sSynchDiscountId) . ' ) ';
        }

        // creating AJAX component
        return $sQAdd;
    }

    /**
     * Removes selected user group(s) from newsletter mailing group.
     */
    public function removeGroupFromNewsletter(): void
    {
        $aRemoveGroups = $this->_getActionIds('oxobject2group.oxid');
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter('delete oxobject2group.* ' . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && \is_array($aRemoveGroups)) {
            $sQ = 'delete from oxobject2group where oxobject2group.oxid in (' . implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups)) . ') ';
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds selected user group(s) to newsletter mailing group.
     */
    public function addGroupToNewsletter(): void
    {
        $aAddGroups = $this->_getActionIds('oxgroups.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aAddGroups = $this->_getAll($this->_addFilter("select $sGroupTable.oxid " . $this->_getQuery()));
        }
        if ($soxId && '-1' !== $soxId && \is_array($aAddGroups)) {
            foreach ($aAddGroups as $sAddgroup) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Application\Model\Object2Group::class);
                $oNewGroup->oxobject2group__oxobjectid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNewGroup->oxobject2group__oxgroupsid = new \OxidEsales\Eshop\Core\Field($sAddgroup);
                $oNewGroup->save();
            }
        }
    }
}
