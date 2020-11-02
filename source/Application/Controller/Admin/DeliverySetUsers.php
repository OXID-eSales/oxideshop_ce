<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin deliveryset User manager.
 * There is possibility to add User, groups
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling Sets -> Users.
 */
class DeliverySetUsers extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
        $oGroups = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oGroups->init('oxgroups');
        $oGroups->selectString('select * from ' . getViewName('oxgroups', $this->_iEditLang));

        $oRoot = new \OxidEsales\Eshop\Application\Model\Groups();
        $oRoot->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field('');
        $oRoot->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field('-- ');
        // rebuild list as we need the "no value" entry at the first position
        $aNewList = [];
        $aNewList[] = $oRoot;

        foreach ($oGroups as $val) {
            $aNewList[$val->oxgroups__oxid->value] = new \OxidEsales\Eshop\Application\Model\Groups();
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxid = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxid->value);
            $aNewList[$val->oxgroups__oxid->value]->oxgroups__oxtitle = new \OxidEsales\Eshop\Core\Field($val->oxgroups__oxtitle->value);
        }

        $oGroups = $aNewList;

        if (isset($soxId) && '-1' !== $soxId) {
            $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySet::class);
            $oDelivery->load($soxId);

            //Disable editing for derived articles
            if ($oDelivery->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $this->_aViewData['allgroups2'] = $oGroups;

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aoc');
        if (1 === $iAoc) {
            $oDeliverysetGroupsAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetGroupsAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetGroupsAjax->getColumns();

            return 'popups/deliveryset_groups.tpl';
        } elseif (2 === $iAoc) {
            $oDeliverysetUsersAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetUsersAjax::class);
            $this->_aViewData['oxajax'] = $oDeliverysetUsersAjax->getColumns();

            return 'popups/deliveryset_users.tpl';
        }

        return 'deliveryset_users.tpl';
    }
}
