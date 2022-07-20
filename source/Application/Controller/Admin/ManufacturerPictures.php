<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Exception\ExceptionToDisplay;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin Manufacturer picture manager.
 * Collects information about Manufacturer's used pictures, there is posibility to
 * upload any other picture, etc.
 * Admin Menu: Master Settings -> Brands/Manufacturers -> Pictures.
 */
class ManufacturerPictures extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads Manufacturer information - pictures, passes data to Smarty
     * engine, returns name of template file "manufacturer_pictures".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData["edit"] = $oManufacturer = oxNew(Manufacturer::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oManufacturer->load($soxId);
            $oManufacturer = $this->updateManufacturer($oManufacturer);
        }

        $this->_aViewData["iManufacturerPicCount"] = Registry::getConfig()->getConfigParam('iManufacturerPicCount');

        if ($this->getViewConfig()->isAltImageServerConfigured()) {
            $config = Registry::getConfig();

            if ($config->getConfigParam('sAltImageUrl')) {
                $this->_aViewData["imageUrl"] = $config->getConfigParam('sAltImageUrl');
            } else {
                $this->_aViewData["imageUrl"] = $config->getConfigParam('sSSLAltImageUrl');
            }
        }

        return "manufacturer_pictures";
    }

    /**
     * Saves (uploads) pictures to server.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig = Registry::getConfig();

        if ($myConfig->isDemoShop()) {
            // disabling uploading pictures if this is demo shop
            $oEx = oxNew(ExceptionToDisplay::class);
            $oEx->setMessage('MANUFACTURER_PICTURES_UPLOADISDISABLED');
            Registry::getUtilsView()->addErrorToDisplay($oEx, false);

            return;
        }

        parent::save();

        $oManufacturer = oxNew(Manufacturer::class);
        if ($oManufacturer->load($this->getEditObjectId())) {
            $oManufacturer->assign(Registry::getRequest()->getRequestEscapedParameter("editval"));
            Registry::getUtilsFile()->processFiles($oManufacturer);

            // Show that no new image added
            if (Registry::getUtilsFile()->getNewFilesCounter() == 0) {
                $oEx = oxNew(ExceptionToDisplay::class);
                $oEx->setMessage('NO_PICTURES_CHANGES');
                Registry::getUtilsView()->addErrorToDisplay($oEx, false);
            }

            $oManufacturer->save();
        }
    }

    /**
     * Deletes selected master picture and all other master pictures
     * where master picture index is higher than currently deleted index.
     *
     * @return null
     */
    public function deletePicture()
    {
        $myConfig = Registry::getConfig();

        if ($myConfig->isDemoShop()) {
            // disabling uploading pictures if this is demo shop
            $oEx = oxNew(ExceptionToDisplay::class);
            $oEx->setMessage('MANUFACTURER_PICTURES_UPLOADISDISABLED');
            Registry::getUtilsView()->addErrorToDisplay($oEx, false);

            return;
        }

        $sOxId = $this->getEditObjectId();
        $iIndex = Registry::getRequest()->getRequestEscapedParameter("masterPicIndex");

        $oManufacturer = oxNew(Manufacturer::class);
        $oManufacturer->load($sOxId);

        $iIndex = (int) $iIndex;
        if ($iIndex > 0) {
            // deleting master picture
            $this->resetMasterPicture($oManufacturer, $iIndex);
        }

        $oManufacturer->save();
    }

    /**
     * Deletes selected master picture
     *
     * @param Manufacturer $oManufacturer       Manufacturer object
     * @param int $iIndex                       master picture index
     */
    protected function resetMasterPicture(Manufacturer $oManufacturer, int $iIndex)
    {
        if ($this->canResetMasterPicture($oManufacturer, $iIndex)) {
            if (!$oManufacturer->isDerived()) {
                $oPicHandler = Registry::getPictureHandler();
                $oPicHandler->deleteManufacturerMasterPicture($oManufacturer, $iIndex);
            }

            //reseting master picture field
            $oManufacturer->{"oxmanufacturers__oxpic" . $iIndex} = new \OxidEsales\Eshop\Core\Field();
        }
    }

    /**
     * Method is used for overloading to update Manufacturer object.
     *
     * @param Manufacturer $oManufacturer
     *
     * @return Manufacturer
     */
    protected function updateManufacturer($oManufacturer)
    {
        return $oManufacturer;
    }

    /**
     * Checks if possible to reset master picture.
     *
     * @param Manufacturer $oManufacturer
     * @param int $masterPictureIndex
     *
     * @return bool
     */
    protected function canResetMasterPicture($oManufacturer, $masterPictureIndex)
    {
        return (bool) $oManufacturer->{"oxmanufacturers__oxpic" . $masterPictureIndex}->value;
    }
}
