<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin category main attributes manager.
 * There is possibility to change attribute description, assign categories to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Gruppen.
 */
class AttributeCategory extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads Attribute categories info, passes it to Smarty engine and
     * returns name of template file "attribute_main.tpl".
     *
     * @return string
     */
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

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oAttributeCategoryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AttributeCategoryAjax::class);
            $this->_aViewData['oxajax'] = $oAttributeCategoryAjax->getColumns();

            return "popups/attribute_category.tpl";
        }

        return "attribute_category.tpl";
    }
}
