<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin article main attributes manager.
 * There is possibility to change attribute description, assign articles to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Main.
 */
class AttributeMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article Attributes info, passes it to Smarty engine and
     * returns name of template file "attribute_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $myConfig = $this->getConfig();

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);
        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        // copy this tree for our article choose
        if (isset($soxId) && $soxId != "-1") {
            // generating category tree for select list
            $this->_createCategoryTree("artcattree", $soxId);
            // load object
            $oAttr->loadInLang($this->_iEditLang, $soxId);

            //Disable editing for derived items
            if ($oAttr->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            $oOtherLang = $oAttr->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
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

        if ($myConfig->getRequestParameter("aoc")) {
            $oAttributeMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AttributeMainAjax::class);
            $this->_aViewData['oxajax'] = $oAttributeMainAjax->getColumns();

            return "popups/attribute_main.tpl";
        }

        return "attribute_main.tpl";
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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

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
        $oAttr->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId($oAttr->getId());
    }
}
