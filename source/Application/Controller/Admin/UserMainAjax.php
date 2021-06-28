<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages user assignment to groups
 */
class UserMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
                                     ['oxid', 'oxobject2group', 0, 0, 1],
                                 ]
    ];

    /**
     * Returns SQL query for data to fetch
     *
     * @return string
     */
    protected function _getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        // looking for table/view
        $sGroupTable = $this->_getViewName('oxgroups');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDeldId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchDelId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sDeldId) {
            $sQAdd = " from $sGroupTable where 1 ";
        } else {
            $sQAdd = " from $sGroupTable left join oxobject2group on oxobject2group.oxgroupsid=$sGroupTable.oxid ";
            $sQAdd .= " where oxobject2group.oxobjectid = " . $oDb->quote($sDeldId);
        }

        if ($sSynchDelId && $sSynchDelId != $sDeldId) {
            $sQAdd .= " and $sGroupTable.oxid not in ( select $sGroupTable.oxid from $sGroupTable left join oxobject2group on oxobject2group.oxgroupsid=$sGroupTable.oxid ";
            $sQAdd .= " where oxobject2group.oxobjectid = " . $oDb->quote($sSynchDelId) . " ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes user from selected user group(s).
     */
    public function removeUserFromGroup()
    {
        $aRemoveGroups = $this->_getActionIds('oxobject2group.oxid');
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = $this->_addFilter("delete oxobject2group.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sQ = "delete from oxobject2group where oxobject2group.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds user to selected user group(s).
     */
    public function addUserToGroup()
    {
        $aAddGroups = $this->_getActionIds('oxgroups.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aAddGroups = $this->_getAll($this->_addFilter("select $sGroupTable.oxid " . $this->_getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aAddGroups)) {
            foreach ($aAddGroups as $sAddgroup) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Application\Model\Object2Group::class);
                $oNewGroup->oxobject2group__oxobjectid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNewGroup->oxobject2group__oxgroupsid = new \OxidEsales\Eshop\Core\Field($sAddgroup);
                $oNewGroup->save();
            }
        }
    }
}
