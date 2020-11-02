<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

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

        // all usergroups
        $oGroups = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oGroups->init('oxgroups');
        $oGroups->selectString('select * from ' . getViewName('oxgroups', $this->_iEditLang));

        $oRoot = new stdClass();
        $oRoot->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field('');
        $oRoot->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field('-- ');
        // rebuild list as we need the "no value" entry at the first position
        $aNewList = [];
        $aNewList[] = $oRoot;

        foreach ($oGroups as $val) {
            $aNewList[$val->oxgroups__oxid->value] = new stdClass();
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxid->value);
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxtitle->value);
        }

        $this->_aViewData['allgroups2'] = $aNewList;

        if (isset($soxId) && '-1' !== $soxId) {
            $oDiscount = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
            $oDiscount->load($soxId);

            if ($oDiscount->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aoc');
        if (1 === $iAoc) {
            $oDiscountGroupsAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountGroupsAjax::class);
            $this->_aViewData['oxajax'] = $oDiscountGroupsAjax->getColumns();

            return 'popups/discount_groups.tpl';
        } elseif (2 === $iAoc) {
            $oDiscountUsersAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountUsersAjax::class);
            $this->_aViewData['oxajax'] = $oDiscountUsersAjax->getColumns();

            return 'popups/discount_users.tpl';
        }

        return 'discount_users.tpl';
    }
}
