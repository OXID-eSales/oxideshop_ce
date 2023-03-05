<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Exception\ExceptionToDisplay;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin manufacturer main screen.
 * Performs collection and updating (on user submit) main item information.
 */
class ManufacturerPicture extends AdminDetailsController
{
    public function render(): string
    {
        parent::render();

        $this->_aViewData['edit'] = $oManufacturer = oxNew(Manufacturer::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != '-1') {
            $oManufacturer->load($soxId);
        }

        return "manufacturer_picture";
    }

    public function save(): void
    {
        if (Registry::getConfig()->isDemoShop()) {
            $oEx = oxNew(ExceptionToDisplay::class);
            $oEx->setMessage('MANUFACTURER_PICTURES_UPLOAD_IS_DISABLED');
            Registry::getUtilsView()->addErrorToDisplay($oEx, false);

            return;
        }

        parent::save();

        $oManufacturer = oxNew(Manufacturer::class);
        if ($oManufacturer->load($this->getEditObjectId())) {
            $oManufacturer->assign(Registry::getRequest()->getRequestEscapedParameter("editval"));
            $oManufacturer = Registry::getUtilsFile()->processFiles($oManufacturer);

            $this->checkNewImagesCount();

            $oManufacturer->save();
            $this->setEditObjectId($oManufacturer->getId());
        }
    }

    private function checkNewImagesCount(): void
    {
        // Show that no new image added
        if (Registry::getUtilsFile()->getNewFilesCounter() == 0) {
            $oEx = oxNew(ExceptionToDisplay::class);
            $oEx->setMessage('NO_PICTURES_CHANGES');
            Registry::getUtilsView()->addErrorToDisplay($oEx, false);
        }
    }
}
