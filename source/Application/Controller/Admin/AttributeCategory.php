<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin category main attributes manager.
 * There is possibility to change attribute description, assign categories to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Gruppen.
 */
class AttributeCategory extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);
            $oAttr->load($soxId);
            $this->_aViewData["edit"] = $oAttr;
        }

        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oAttributeCategoryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AttributeCategoryAjax::class);
            $this->_aViewData['oxajax'] = $oAttributeCategoryAjax->getColumns();

            return "popups/attribute_category";
        }

        return "attribute_category";
    }
}
