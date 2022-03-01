<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article main delivery manager.
 * There is possibility to change delivery name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 */
class DeliveryArticles extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();

        if (isset($soxId) && $soxId != "-1") {
            $this->createCategoryTree("artcattree");

            // load object
            $oDelivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);
            $oDelivery->load($soxId);
            $this->_aViewData["edit"] = $oDelivery;

            //Disable editing for derived articles
            if ($oDelivery->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }
        }

        $iAoc = Registry::getRequest()->getRequestEscapedParameter("aoc");
        if ($iAoc == 1) {
            $oDeliveryArticlesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryArticlesAjax::class);
            $this->_aViewData['oxajax'] = $oDeliveryArticlesAjax->getColumns();

            return "popups/delivery_articles";
        } elseif ($iAoc == 2) {
            $oDeliveryCategoriesAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryCategoriesAjax::class);
            $this->_aViewData['oxajax'] = $oDeliveryCategoriesAjax->getColumns();

            return "popups/delivery_categories";
        }

        return "delivery_articles";
    }
}
