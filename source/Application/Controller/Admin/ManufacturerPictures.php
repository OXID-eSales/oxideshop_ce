<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Manufacturer;
/**
 * Admin Manufacturer picture manager.
 * Collects information about Manufacturer's used pictures, there is posibility to
 * upload any other picture, etc.
 * Admin Menu: Master Settings -> Brands/Manufacturers -> Pictures.
 */
class ManufacturerPictures extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $this->_aViewData["edit"] = $oManufacturer = oxNew(Manufacturer::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            $oManufacturer->load($soxId);
        }

        return "manufacturer_pictures";
    }
}
