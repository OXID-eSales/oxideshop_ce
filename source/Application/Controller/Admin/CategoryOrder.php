<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article categories order manager.
 * There is possibility to change category sorting.
 * Admin Menu: Manage Products -> Categories -> Order.
 */
class CategoryOrder extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article category ordering info, passes it to Smarty
     * engine and returns name of template file "category_order.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        // resetting
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('neworder_sess', null);

        $soxId = $this->getEditObjectId();

        if (isset($soxId) && '-1' !== $soxId) {
            // load object
            $oCategory->load($soxId);

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aoc')) {
            $oCategoryOrderAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\CategoryOrderAjax::class);
            $this->_aViewData['oxajax'] = $oCategoryOrderAjax->getColumns();

            return 'popups/category_order.tpl';
        }

        return 'category_order.tpl';
    }
}
