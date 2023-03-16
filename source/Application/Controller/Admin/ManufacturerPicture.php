<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Core\Exception\ExceptionToDisplay;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin manufacturer picture screen.
 * Handle manufacturer picture actions.
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
            $this->showError('MANUFACTURER_PICTURES_UPLOAD_IS_DISABLED');

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

    public function deletePicture(): void
    {
        if (Registry::getConfig()->isDemoShop()) {
            $this->showError('MANUFACTURER_PICTURES_UPLOAD_IS_DISABLED');

            return;
        }

        $pictureFieldName = Registry::getRequest()->getRequestEscapedParameter('masterPictureField');
        if (empty($pictureFieldName)) {
            return;
        }

        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->load($this->getEditObjectId());

        $pictureKey = 'oxmanufacturers__' . $pictureFieldName;
        $pictureType = match ($pictureFieldName) {
            'oxicon' => 'MICO',
            default => false,
        };

        if ($pictureType !== false) {
            $manufacturer->deletePicture($manufacturer->$pictureKey->value, $pictureType, $pictureFieldName);

            $manufacturer->$pictureKey = new Field();
            $manufacturer->save();
        }
    }

    private function checkNewImagesCount(): void
    {
        if (Registry::getUtilsFile()->getNewFilesCounter() == 0) {
            $this->showError('NO_PICTURES_CHANGES');
        }
    }

    private function showError(string $message, bool $isBlFull = false): void
    {
        $oEx = oxNew(ExceptionToDisplay::class);
        $oEx->setMessage($message);
        Registry::getUtilsView()->addErrorToDisplay($oEx, $isBlFull);
    }
}
