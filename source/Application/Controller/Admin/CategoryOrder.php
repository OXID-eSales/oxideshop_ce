<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article categories order manager.
 * There is possibility to change category sorting.
 * Admin Menu: Manage Products -> Categories -> Order.
 */
class CategoryOrder extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article category ordering info, passes it to template engine
     * engine and returns name of template file "category_order".
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

        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oCategory->load($soxId);

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }
        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oCategoryOrderAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\CategoryOrderAjax::class);
            $this->_aViewData['oxajax'] = $oCategoryOrderAjax->getColumns();

            return "popups/category_order";
        }

        return "category_order";
    }
}
