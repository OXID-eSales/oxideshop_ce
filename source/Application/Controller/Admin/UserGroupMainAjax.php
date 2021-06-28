<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages users assignment to groups
 */
class UserGroupMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,  visible, multilanguage, ident
        ['oxusername', 'oxuser', 1, 0, 0],
        ['oxlname', 'oxuser', 0, 0, 0],
        ['oxfname', 'oxuser', 0, 0, 0],
        ['oxstreet', 'oxuser', 0, 0, 0],
        ['oxstreetnr', 'oxuser', 0, 0, 0],
        ['oxcity', 'oxuser', 0, 0, 0],
        ['oxzip', 'oxuser', 0, 0, 0],
        ['oxfon', 'oxuser', 0, 0, 0],
        ['oxbirthdate', 'oxuser', 0, 0, 0],
        ['oxid', 'oxuser', 0, 0, 1],
    ],
                                 'container2' => [
                                     ['oxusername', 'oxuser', 1, 0, 0],
                                     ['oxlname', 'oxuser', 0, 0, 0],
                                     ['oxfname', 'oxuser', 0, 0, 0],
                                     ['oxstreet', 'oxuser', 0, 0, 0],
                                     ['oxstreetnr', 'oxuser', 0, 0, 0],
                                     ['oxcity', 'oxuser', 0, 0, 0],
                                     ['oxzip', 'oxuser', 0, 0, 0],
                                     ['oxfon', 'oxuser', 0, 0, 0],
                                     ['oxbirthdate', 'oxuser', 0, 0, 0],
                                     ['oxid', 'oxobject2group', 0, 0, 1],
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        // looking for table/view
        $sUserTable = $this->getViewName('oxuser');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sRoleId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchRoleId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sRoleId) {
            $sQAdd = " from $sUserTable where 1 ";
        } else {
            $sQAdd = " from $sUserTable, oxobject2group where $sUserTable.oxid=oxobject2group.oxobjectid and ";
            $sQAdd .= " oxobject2group.oxgroupsid = " . $oDb->quote($sRoleId);
        }

        if ($sSynchRoleId && $sSynchRoleId != $sRoleId) {
            $sQAdd .= " and $sUserTable.oxid not in ( select $sUserTable.oxid from $sUserTable, oxobject2group where $sUserTable.oxid=oxobject2group.oxobjectid and ";
            $sQAdd .= " oxobject2group.oxgroupsid = " . $oDb->quote($sSynchRoleId);
            if (!$myConfig->getConfigParam('blMallUsers')) {
                $sQAdd .= " and $sUserTable.oxshopid = '" . $myConfig->getShopId() . "' ";
            }
            $sQAdd .= " ) ";
        }

        if (!$myConfig->getConfigParam('blMallUsers')) {
            $sQAdd .= " and $sUserTable.oxshopid = '" . $myConfig->getShopId() . "' ";
        }

        return $sQAdd;
    }

    /**
     * Removes User from group
     */
    public function removeUserFromUGroup()
    {
        $aRemoveGroups = $this->getActionIds('oxobject2group.oxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = $this->addFilter("delete oxobject2group.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sQ = "delete from oxobject2group where oxobject2group.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds User to group
     */
    public function addUserToUGroup()
    {
        $aAddUsers = $this->getActionIds('oxuser.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sUserTable = $this->getViewName('oxuser');
            $aAddUsers = $this->getAll($this->addFilter("select $sUserTable.oxid " . $this->getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aAddUsers)) {
            foreach ($aAddUsers as $sAdduser) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Application\Model\Object2Group::class);
                $oNewGroup->oxobject2group__oxobjectid = new \OxidEsales\Eshop\Core\Field($sAdduser);
                $oNewGroup->oxobject2group__oxgroupsid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNewGroup->save();
            }
        }
    }
}
