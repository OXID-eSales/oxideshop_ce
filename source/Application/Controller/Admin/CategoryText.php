<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin article categories text manager.
 * Category text/description manager, enables editing of text.
 * Admin Menu: Manage Products -> Categories -> Text.
 */
class CategoryText extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $iCatLang = Registry::getRequest()->getRequestEscapedParameter("catlang");

            if (!isset($iCatLang)) {
                $iCatLang = $this->_iEditLang;
            }

            $this->_aViewData["catlang"] = $iCatLang;

            $oCategory->loadInLang($iCatLang, $soxId);

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            foreach (\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames() as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        $this->_aViewData["editor"] = $this->generateTextEditor("100%", 300, $oCategory, "oxcategories__oxlongdesc", "list.css");

        return "category_text";
    }

    /**
     * Saves category description text to DB.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $iCatLang = Registry::getRequest()->getRequestEscapedParameter("catlang");
        $iCatLang = $iCatLang ? $iCatLang : 0;

        if ($soxId != "-1") {
            $oCategory->loadInLang($iCatLang, $soxId);
        } else {
            $aParams['oxcategories__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oCategory->isDerived()) {
            return;
        }

        $oCategory->setLanguage(0);
        $oCategory->assign($aParams);
        $oCategory->setLanguage($iCatLang);
        $oCategory->save();

        // set oxid if inserted
        $this->setEditObjectId($oCategory->getId());
    }
}
