<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin article main delivery manager.
 * There is possibility to change delivery name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 */
class DeliveryArticles extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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

        if (isset($soxId) && $soxId != "-1") {
            $this->_createCategoryTree("artcattree");

            // load object
            $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);
            $oDelivery->load($soxId);
            $this->_aViewData["edit"] = $oDelivery;

            //Disable editing for derived articles
            if ($oDelivery->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oDeliveryArticlesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryArticlesAjax::class);
            $this->_aViewData['oxajax'] = $oDeliveryArticlesAjax->getColumns();

            return "popups/delivery_articles.tpl";
        } elseif ($iAoc == 2) {
            $oDeliveryCategoriesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryCategoriesAjax::class);
            $this->_aViewData['oxajax'] = $oDeliveryCategoriesAjax->getColumns();

            return "popups/delivery_categories.tpl";
        }

        return "delivery_articles.tpl";
    }
}
