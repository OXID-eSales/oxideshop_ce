<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin manufacturer main screen.
 * Performs collection and updating (on user submit) main item information.
 */
class ManufacturerMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(),
     * and returns name of template file
     * "manufacturer_main".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oManufacturer->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oManufacturer->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oManufacturer;

            // category tree
            $this->createCategoryTree("artcattree");

            //Disable editing for derived articles
            if ($oManufacturer->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $aLang = array_diff(Registry::getLang()->getLanguageNames(), $oOtherLang);
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

        if ($this->getViewConfig()->isAltImageServerConfigured()) {
            $config = Registry::getConfig();

            if ($config->getConfigParam('sAltImageUrl')) {
                $this->_aViewData["imageUrl"] = $config->getConfigParam('sAltImageUrl');
            } else {
                $this->_aViewData["imageUrl"] = $config->getConfigParam('sSSLAltImageUrl');
            }
        }

        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oManufacturerMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class);
            $this->_aViewData['oxajax'] = $oManufacturerMainAjax->getColumns();

            return "popups/manufacturer_main";
        }

        return "manufacturer_main";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (!isset($aParams['oxmanufacturers__oxactive'])) {
            $aParams['oxmanufacturers__oxactive'] = 0;
        }

        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);

        if ($soxId != "-1") {
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxmanufacturers__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oManufacturer->isDerived()) {
            return;
        }

        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer->save();

        // set oxid if inserted
        $this->setEditObjectId($oManufacturer->getId());
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return mixed
     */
    public function saveInnLang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (!isset($aParams['oxmanufacturers__oxactive'])) {
            $aParams['oxmanufacturers__oxactive'] = 0;
        }

        $oManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);

        if ($soxId != "-1") {
            $oManufacturer->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxmanufacturers__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oManufacturer->isDerived()) {
            return;
        }

        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer->save();

        // set oxid if inserted
        $this->setEditObjectId($oManufacturer->getId());
    }
}
