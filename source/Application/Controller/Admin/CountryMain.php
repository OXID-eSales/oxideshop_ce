<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Country;
use stdClass;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article main selectlist manager.
 * Performs collection and updatind (on user submit) main item information.
 */
class CountryMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oCountry = oxNew(Country::class);
            $oCountry->loadInLang($this->_iEditLang, $soxId);

            if ($oCountry->isForeignCountry()) {
                $this->_aViewData["blForeignCountry"] = true;
            } else {
                $this->_aViewData["blForeignCountry"] = false;
            }

            $oOtherLang = $oCountry->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oCountry->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oCountry;

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
        } else {
            $this->_aViewData["blForeignCountry"] = true;
        }

        return "country_main";
    }

    /**
     * Saves selection list parameters changes.
     */
    public function save()
    {
        parent::save();

        $oxidId = $this->getEditObjectId();
        $queryParameters = Registry::getRequest()->getRequestEscapedParameter("editval");

        if ($queryParameters['oxcountry__oxvatstatus'] === '1' && empty($queryParameters['oxcountry__oxvatinprefix'])) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_INPUT_VAT_PREFIX_EMPTY');
            return;
        }

        if (!isset($queryParameters['oxcountry__oxactive'])) {
            $queryParameters['oxcountry__oxactive'] = 0;
        }

        $country = oxNew(Country::class);

        if ($oxidId != "-1") {
            $country->loadInLang($this->_iEditLang, $oxidId);
        } else {
            $queryParameters['oxcountry__oxid'] = null;
        }

        $country->setLanguage(0);
        $country->assign($queryParameters);
        $country->setLanguage($this->_iEditLang);
        $country = Registry::getUtilsFile()->processFiles($country);
        $country->save();

        $this->setEditObjectId($country->getId());
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (!isset($aParams['oxcountry__oxactive'])) {
            $aParams['oxcountry__oxactive'] = 0;
        }

        $oCountry = oxNew(Country::class);

        if ($soxId != "-1") {
            $oCountry->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxcountry__oxid'] = null;
            //$aParams = $oCountry->ConvertNameArray2Idx( $aParams);
        }

        $oCountry->setLanguage(0);
        $oCountry->assign($aParams);
        $oCountry->setLanguage($this->_iEditLang);

        $oCountry->save();

        // set oxid if inserted
        $this->setEditObjectId($oCountry->getId());
    }
}
