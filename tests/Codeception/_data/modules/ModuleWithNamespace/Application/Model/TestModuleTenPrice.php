<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\ModuleWithNamespace\Application\Model;

final class TestModuleTenPrice extends TestModuleTenPrice_parent
{
    protected $originalPrice;

    /**
     * Triple the original price
     *
     * @return double
     */
    public function getPrice()
    {
        $this->originalPrice = parent::getPrice();

        return $this->originalPrice * 3;
    }
}
