<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin voucherserie groups manager.
 * Collects and manages information about user groups, added to one or another
 * serie of vouchers.
 * Admin Menu: Shop Settings -> Vouchers -> Groups.
 */
class VoucherSerieGroups extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxlist and oxvoucherserie
     * objects, passes it's data to Smarty engine and returns name of template
     * file "voucherserie_groups.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oVoucherSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
            $oVoucherSerie->load($soxId);
            $oVoucherSerie->setUserGroups();
            $this->_aViewData["edit"] = $oVoucherSerie;

            //Disable editing for derived items
            if ($oVoucherSerie->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oVoucherSerieGroupsAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class);
            $this->_aViewData['oxajax'] = $oVoucherSerieGroupsAjax->getColumns();

            return "popups/voucherserie_groups.tpl";
        }

        return "voucherserie_groups.tpl";
    }
}
