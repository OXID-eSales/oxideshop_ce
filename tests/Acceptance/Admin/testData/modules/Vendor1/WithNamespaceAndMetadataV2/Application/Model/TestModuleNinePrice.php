<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Model;

/**
 * Class TestModuleNinePrice
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Model
 */
class TestModuleNinePrice extends TestModuleNinePrice_parent
{
    protected $originalPrice = null;

    /**
     * Double the original price
     *
     * @return double
     */
    public function getPrice()
    {
        $this->originalPrice = parent::getPrice();

        return $this->originalPrice * 2;
    }
}
