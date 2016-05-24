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

/**
 * Class manages promotion groups
 */
class ActionsGroupsAjax extends \ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        // field , table,  visible, multilanguage, ident
        'container1' => array(
            array('oxtitle', 'oxgroups', 1, 0, 0),
            array('oxid', 'oxgroups', 0, 0, 0),
            array('oxid', 'oxgroups', 0, 0, 1),
        ),
         'container2' => array(
             array('oxtitle', 'oxgroups', 1, 0, 0),
             array('oxid', 'oxgroups', 0, 0, 0),
             array('oxid', 'oxobject2action', 0, 0, 1),
         )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        // active AJAX component
        $sGroupTable = $this->_getViewName('oxgroups');
        $oDb = oxDb::getDb();

        $sId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $sSynchId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

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
        $aRemoveGroups = $this->_getActionIds('oxobject2action.oxid');
        if ($this->getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxobject2action.* " . $this->_getQuery());
            oxDb::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sRemoveGroups = implode(", ", oxDb::getDb()->quoteArray($aRemoveGroups));
            $sQ = "delete from oxobject2action where oxobject2action.oxid in (" . $sRemoveGroups . ") ";
            oxDb::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds user group to promotion
     *
     * @return bool Whether at least one promotion was added.
     */
    public function addPromotionGroup()
    {
        $aChosenGroup = $this->_getActionIds('oxgroups.oxid');
        $soxId = $this->getConfig()->getRequestParameter('synchoxid');

        if ($this->getConfig()->getRequestParameter('all')) {
            $sGroupTable = $this->_getViewName('oxgroups');
            $aChosenGroup = $this->_getAll($this->_addFilter("select $sGroupTable.oxid " . $this->_getQuery()));
        }

        $promotionAdded = false;
        if ($soxId && $soxId != "-1" && is_array($aChosenGroup)) {
            foreach ($aChosenGroup as $sChosenGroup) {
                $oObject2Promotion = oxNew("oxBase");
                $oObject2Promotion->init('oxobject2action');
                $oObject2Promotion->oxobject2action__oxactionid = new oxField($soxId);
                $oObject2Promotion->oxobject2action__oxobjectid = new oxField($sChosenGroup);
                $oObject2Promotion->oxobject2action__oxclass = new oxField("oxgroups");
                $oObject2Promotion->save();
            }

            $promotionAdded = true;
        }

        return $promotionAdded;
    }
}
