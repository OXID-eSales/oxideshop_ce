<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin article main attributes manager.
 * There is possibility to change attribute description, assign articles to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Main.
 */
class AttributeMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);
        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        // copy this tree for our article choose
        if (isset($soxId) && $soxId != "-1") {
            // generating category tree for select list
            $this->createCategoryTree("artcattree", $soxId);
            // load object
            $oAttr->loadInLang($this->_iEditLang, $soxId);

            //Disable editing for derived items
            if ($oAttr->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            $oOtherLang = $oAttr->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oAttr->loadInLang(key($oOtherLang), $soxId);
            }

            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        $this->_aViewData["edit"] = $oAttr;

        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oAttributeMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AttributeMainAjax::class);
            $this->_aViewData['oxajax'] = $oAttributeMainAjax->getColumns();

            return "popups/attribute_main";
        }

        return "attribute_main";
    }

    /**
     * Saves article attributes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);

        if ($soxId != "-1") {
            $oAttr->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxattribute__oxid'] = null;
            //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        }

        //Disable editing for derived items
        if ($oAttr->isDerived()) {
            return;
        }

        $oAttr->setLanguage(0);
        $oAttr->assign($aParams);
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oAttr);
        $oAttr->save();

        $this->setEditObjectId($oAttr->getId());
    }

    /**
     * Saves attribute data to different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);

        if ($soxId != "-1") {
            $oAttr->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxattribute__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oAttr->isDerived()) {
            return;
        }

        $oAttr->setLanguage(0);
        $oAttr->assign($aParams);

        // apply new language
        $oAttr->setLanguage(Registry::getRequest()->getRequestEscapedParameter("new_lang"));
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId($oAttr->getId());
    }
}
