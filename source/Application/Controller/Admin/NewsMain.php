<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;
use oxField;

/**
 * Admin article main news manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer Info -> News -> Main.
 * @deprecated 6.5.6 "News" feature will be removed completely
 */
class NewsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxlist object and
     * collects user groups information, passes data to Smarty engine,
     * returns name of template file "news_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oNews = oxNew(\OxidEsales\Eshop\Application\Model\News::class);
            $oNews->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oNews->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oNews->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oNews;

            //Disable editing for derived items
            if ($oNews->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $this->_aViewData["posslang"] = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oNewsMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\NewsMainAjax::class);
            $this->_aViewData['oxajax'] = $oNewsMainAjax->getColumns();

            return "popups/news_main.tpl";
        }

        return "news_main.tpl";
    }

    /**
     * Saves news parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxnews__oxactive'])) {
            $aParams['oxnews__oxactive'] = 0;
        }
        // creating fake object to save correct time value
        if (!$aParams['oxnews__oxdate']) {
            $aParams['oxnews__oxdate'] = "";
        }

        $oConvObject = new \OxidEsales\Eshop\Core\Field();
        $oConvObject->fldmax_length = 0;
        $oConvObject->fldtype = "date";
        $oConvObject->value = $aParams['oxnews__oxdate'];
        $aParams['oxnews__oxdate'] = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oConvObject, true);

        $oNews = oxNew(\OxidEsales\Eshop\Application\Model\News::class);

        if ($soxId != "-1") {
            $oNews->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxnews__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oNews->isDerived()) {
            return;
        }

        //$aParams = $oNews->ConvertNameArray2Idx( $aParams);

        $oNews->setLanguage(0);
        $oNews->assign($aParams);
        $oNews->setLanguage($this->_iEditLang);
        $oNews->save();

        // set oxid if inserted
        $this->setEditObjectId($oNews->getId());
    }

    /**
     * Saves news parameters in different language.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxnews__oxactive'])) {
            $aParams['oxnews__oxactive'] = 0;
        }

        parent::save();

        // creating fake object to save correct time value
        if (!$aParams['oxnews__oxdate']) {
            $aParams['oxnews__oxdate'] = "";
        }

        $oConvObject = new \OxidEsales\Eshop\Core\Field();
        $oConvObject->fldmax_length = 0;
        $oConvObject->fldtype = "date";
        $oConvObject->value = $aParams['oxnews__oxdate'];
        $aParams['oxnews__oxdate'] = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oConvObject, true);

        $oNews = oxNew(\OxidEsales\Eshop\Application\Model\News::class);

        if ($soxId != "-1") {
            $oNews->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxnews__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oNews->isDerived()) {
            return;
        }

        //$aParams = $oNews->ConvertNameArray2Idx( $aParams);
        $oNews->setLanguage(0);
        $oNews->assign($aParams);

        // apply new language
        $oNews->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
        $oNews->save();

        // set oxid if inserted
        $this->setEditObjectId($oNews->getId());
    }
}
