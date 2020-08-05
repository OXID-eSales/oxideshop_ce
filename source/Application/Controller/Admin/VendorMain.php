<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin vendor main screen.
 * Performs collection and updating (on user submit) main item information.
 */
class VendorMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(),
     * and returns name of template file
     * "vendor_main".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
            $oVendor->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oVendor->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oVendor->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oVendor;

            // category tree
            $this->createCategoryTree("artcattree");

            //Disable editing for derived articles
            if ($oVendor->isDerived()) {
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
            $oVendorMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class);
            $this->_aViewData['oxajax'] = $oVendorMainAjax->getColumns();

            return "popups/vendor_main";
        }

        return "vendor_main";
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

        if (!isset($aParams['oxvendor__oxactive'])) {
            $aParams['oxvendor__oxactive'] = 0;
        }

        $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        if ($soxId != "-1") {
            $oVendor->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxvendor__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oVendor->isDerived()) {
            return;
        }

        $oVendor->setLanguage(0);
        $oVendor->assign($aParams);
        $oVendor->setLanguage($this->_iEditLang);
        $oVendor = Registry::getUtilsFile()->processFiles($oVendor);
        $oVendor->save();

        // set oxid if inserted
        $this->setEditObjectId($oVendor->getId());
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return mixed
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (!isset($aParams['oxvendor__oxactive'])) {
            $aParams['oxvendor__oxactive'] = 0;
        }

        $oVendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);

        if ($soxId != "-1") {
            $oVendor->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxvendor__oxid'] = null;
        }

        //Disable editing for derived articles
        if ($oVendor->isDerived()) {
            return;
        }

        $oVendor->setLanguage(0);
        $oVendor->assign($aParams);
        $oVendor->setLanguage($this->_iEditLang);
        $oVendor = Registry::getUtilsFile()->processFiles($oVendor);
        $oVendor->save();

        // set oxid if inserted
        $this->setEditObjectId($oVendor->getId());
    }
}
