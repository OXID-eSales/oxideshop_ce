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

        //$aParams = $oManufacturer->ConvertNameArray2Idx( $aParams);
        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer = Registry::getUtilsFile()->processFiles($oManufacturer);
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

        //$aParams = $oManufacturer->ConvertNameArray2Idx( $aParams);
        $oManufacturer->setLanguage(0);
        $oManufacturer->assign($aParams);
        $oManufacturer->setLanguage($this->_iEditLang);
        $oManufacturer = Registry::getUtilsFile()->processFiles($oManufacturer);
        $oManufacturer->save();

        // set oxid if inserted
        $this->setEditObjectId($oManufacturer->getId());
    }

    /**
     * Deletes selected master picture.
     *
     * @return null
     */
    public function deletePicture()
    {
        $myConfig = Registry::getConfig();

        if ($myConfig->isDemoShop()) {
            // disabling uploading pictures if this is demo shop
            $oEx = new \OxidEsales\Eshop\Core\Exception\ExceptionToDisplay();
            $oEx->setMessage('MANUFACTURER_PICTURES_UPLOADISDISABLED');

            /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
            $oUtilsView = Registry::getUtilsView();

            $oUtilsView->addErrorToDisplay($oEx, false);

            return;
        }

        $sOxId = $this->getEditObjectId();
        $sField = Registry::getRequest()->getRequestEscapedParameter('masterPicField');
        if (empty($sField)) {
            return;
        }

        /** @var \OxidEsales\Eshop\Application\Model\Manufacturer $oItem */
        $oItem = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $oItem->load($sOxId);
        $this->deleteManufacturerPicture($oItem, $sField);
    }

    /**
     * Delete manufacturer picture, specified in $sField parameter
     *
     * @param \OxidEsales\Eshop\Application\Model\Manufacturer $item  active manufacturer object
     * @param string                                           $field picture field name
     *
     * @return null
     */
    protected function deleteManufacturerPicture($item, $field)
    {
        if ($item->isDerived()) {
            return;
        }

        $myConfig = Registry::getConfig();
        $sItemKey = 'oxmanufacturers__' . $field;

        switch ($field) {
            case 'oxthumb':
                $sImgType = 'TM';
                break;

            case 'oxicon':
                $sImgType = 'MICO';
                break;

            case 'oxpromoicon':
                $sImgType = 'MPICO';
                break;

            case 'oxpic':
                $sImgType = 'MPIC';
                break;

            default:
                $sImgType = false;
        }

        if ($sImgType !== false) {
            /** @var \OxidEsales\Eshop\Core\UtilsPic $myUtilsPic */
            $myUtilsPic = Registry::getUtilsPic();
            /** @var \OxidEsales\Eshop\Core\UtilsFile $oUtilsFile */
            $oUtilsFile = Registry::getUtilsFile();

            $sDir = $myConfig->getPictureDir(false);
            $myUtilsPic->safePictureDelete($item->$sItemKey->value, $sDir . $oUtilsFile->getImageDirByType($sImgType), 'oxmanufacturers', $field);

            $item->$sItemKey = new \OxidEsales\Eshop\Core\Field();
            $item->save();
        }
    }
}
