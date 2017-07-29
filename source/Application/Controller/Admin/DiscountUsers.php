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
use stdClass;

/**
 * Admin article main discount manager.
 * There is possibility to change discount name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 */
class DiscountUsers extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Executes parent method parent::render(), creates discount category tree,
     * passes data to Smarty engine and returns name of template file "discount_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $sSelGroup = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("selgroup");

        // all usergroups
        $oGroups = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oGroups->init('oxgroups');
        $oGroups->selectString("select * from " . getViewName("oxgroups", $this->_iEditLang));

        $oRoot = new stdClass();
        $oRoot->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field("");
        $oRoot->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field("-- ");
        // rebuild list as we need the "no value" entry at the first position
        $aNewList = array();
        $aNewList[] = $oRoot;

        foreach ($oGroups as $val) {
            $aNewList[$val->oxgroups__oxid->value] = new stdClass();
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxid->value);
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxtitle->value);
        }

        $this->_aViewData["allgroups2"] = $aNewList;

        if (isset($soxId) && $soxId != "-1") {
            $oDiscount = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
            $oDiscount->load($soxId);

            if ($oDiscount->isDerived()) {
                $this->_aViewData["readonly"] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oDiscountGroupsAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountGroupsAjax::class);
            $this->_aViewData['oxajax'] = $oDiscountGroupsAjax->getColumns();

            return "popups/discount_groups.tpl";
        } elseif ($iAoc == 2) {
            $oDiscountUsersAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountUsersAjax::class);
            $this->_aViewData['oxajax'] = $oDiscountUsersAjax->getColumns();

            return "popups/discount_users.tpl";
        }

        return "discount_users.tpl";
    }
}
