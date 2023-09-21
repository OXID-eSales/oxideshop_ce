<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Model;

final class TestModuleNinePrice extends TestModuleNinePrice_parent
{
    protected $originalPrice;

    /**
     * @return double
     */
    public function getPrice()
    {
        $this->originalPrice = parent::getPrice();

        return $this->originalPrice * 2;
    }
}
