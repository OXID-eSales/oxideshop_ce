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
use oxField;
use oxGroups;

/**
 * Admin deliveryset User manager.
 * There is possibility to add User, groups
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling Sets -> Users.
 */
class DeliverySetUsers extends \oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates delivery category tree,
     * passes data to Smarty engine and returns name of template file "delivery_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();

        // all usergroups
        $oGroups = oxNew('oxlist');
        $oGroups->init('oxgroups');
        $oGroups->selectString("select * from " . getViewName("oxgroups", $this->_iEditLang));

        $oRoot = new oxgroups();
        $oRoot->oxgroups__oxid = new oxField("");
        $oRoot->oxgroups__oxtitle = new oxField("-- ");
        // rebuild list as we need the "no value" entry at the first position
        $aNewList = array();
        $aNewList[] = $oRoot;

        foreach ($oGroups as $val) {
            $aNewList[$val->oxgroups__oxid->value] = new oxGroups();
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxid = new oxField($val->oxgroups__oxid->value);
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxtitle = new oxField($val->oxgroups__oxtitle->value);
        }

        $oGroups = $aNewList;

        if (isset($soxId) && $soxId != "-1") {
            $oDelivery = oxNew("oxdeliveryset");
            $oDelivery->load($soxId);

            //Disable editing for derived articles
            if ($oDelivery->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $this->_aViewData["allgroups2"] = $oGroups;

        $iAoc = oxRegistry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oDeliverysetGroupsAjax = oxNew('deliveryset_groups_ajax');
            $this->_aViewData['oxajax'] = $oDeliverysetGroupsAjax->getColumns();

            return "popups/deliveryset_groups.tpl";
        } elseif ($iAoc == 2) {
            $oDeliverysetUsersAjax = oxNew('deliveryset_users_ajax');
            $this->_aViewData['oxajax'] = $oDeliverysetUsersAjax->getColumns();

            return "popups/deliveryset_users.tpl";
        }

        return "deliveryset_users.tpl";
    }
}
