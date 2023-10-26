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

        $this->_aViewData['edit'] = $manufacturer = oxNew(Manufacturer::class);

        $oxid = $this->getEditObjectId();
        if (isset($oxid) && $oxid != '-1') {
            $manufacturer->load($oxid);
        }

        return "manufacturer_picture";
    }

    public function save(): void
    {
        if (Registry::getConfig()->isDemoShop()) {
            $this->showError('MANUFACTURER_PICTURES_UPLOAD_IS_DISABLED');

            return;
        }

        if (!$this->validateRequestImages()) {
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE');
            return;
        }

        parent::save();

        $manufacturer = oxNew(Manufacturer::class);
        if ($manufacturer->load($this->getEditObjectId())) {
            $this->fetchChanges($manufacturer);
            $manufacturer->assign(Registry::getRequest()->getRequestEscapedParameter("editval"));
            $manufacturer = Registry::getUtilsFile()->processFiles($manufacturer);

            $this->checkNewImagesCount();

            $manufacturer->save();
            $this->setEditObjectId($manufacturer->getId());
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
        $pictureType = $manufacturer->getImageType($pictureFieldName);

        if ($pictureType !== false) {
            $manufacturer->deletePicture($manufacturer->$pictureKey->value, $pictureType, $pictureFieldName);

            $manufacturer->$pictureKey = new Field();
            $manufacturer->save();
        }
    }

    private function fetchChanges(Manufacturer $manufacturer): array
    {
        $changes = [];

        foreach (Registry::getRequest()->getRequestEscapedParameter("editval") as $fieldName => $value) {
            if ($manufacturer->$fieldName->value !== $value) {
                $changes[] = $manufacturer->$fieldName->value;
            }
        }

        return $changes;
    }

    private function checkNewImagesCount(): void
    {
        if (Registry::getUtilsFile()->getNewFilesCounter() == 0) {
            $this->showError('NO_PICTURES_CHANGES');
        }
    }

    private function showError(string $message, bool $isBlFull = false): void
    {
        $exception = oxNew(ExceptionToDisplay::class);
        $exception->setMessage($message);
        Registry::getUtilsView()->addErrorToDisplay($exception, $isBlFull);
    }
}
