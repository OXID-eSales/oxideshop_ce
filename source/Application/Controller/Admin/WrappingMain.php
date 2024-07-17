<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use stdClass;

/**
 * Admin wrapping main manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: System Administration -> Wrapping -> Main.
 */
class WrappingMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oWrapping = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class);
            $oWrapping->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oWrapping->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oWrapping->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oWrapping;

            //Disable editing for derived articles
            if ($oWrapping->isDerived()) {
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
            $this->_aViewData["imageUrl"] = ContainerFacade::getParameter('oxid_alternative_image_url');
        }

        return "wrapping_main";
    }

    /**
     * Saves main wrapping parameters.
     *
     * @return null
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (!$this->validateRequestImages()) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE');
            return;
        }

        // checkbox handling
        if (!isset($aParams['oxwrapping__oxactive'])) {
            $aParams['oxwrapping__oxactive'] = 0;
        }

        $oWrapping = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class);

        if ($soxId != "-1") {
            $oWrapping->loadInLang($this->_iEditLang, $soxId);
            // #1173M - not all pic are deleted, after article is removed
            Registry::getUtilsPic()->overwritePic($oWrapping, 'oxwrapping', 'oxpic', 'WP', '0', $aParams, Registry::getConfig()->getPictureDir(false));
        } else {
            $aParams['oxwrapping__oxid'] = null;
            //$aParams = $oWrapping->ConvertNameArray2Idx( $aParams);
        }

        //Disable editing for derived articles
        if ($oWrapping->isDerived()) {
            return;
        }

        $oWrapping->setLanguage(0);
        $oWrapping->assign($aParams);
        $oWrapping->setLanguage($this->_iEditLang);

        $oWrapping = Registry::getUtilsFile()->processFiles($oWrapping);
        $oWrapping->save();

        // set oxid if inserted
        $this->setEditObjectId($oWrapping->getId());
    }

    /**
     * Saves main wrapping parameters.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        // checkbox handling
        if (!isset($aParams['oxwrapping__oxactive'])) {
            $aParams['oxwrapping__oxactive'] = 0;
        }

        $oWrapping = oxNew(\OxidEsales\Eshop\Application\Model\Wrapping::class);

        if ($soxId != "-1") {
            $oWrapping->load($soxId);
        } else {
            $aParams['oxwrapping__oxid'] = null;
            //$aParams = $oWrapping->ConvertNameArray2Idx( $aParams);
        }

        //Disable editing for derived articles
        if ($oWrapping->isDerived()) {
            return;
        }

        $oWrapping->setLanguage(0);
        $oWrapping->assign($aParams);
        $oWrapping->setLanguage($this->_iEditLang);

        $oWrapping = Registry::getUtilsFile()->processFiles($oWrapping);
        $oWrapping->save();

        // set oxid if inserted
        $this->setEditObjectId($oWrapping->getId());
    }
}
