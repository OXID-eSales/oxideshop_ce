<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Component;

use OxidEsales\Eshop\Application\Component\ShopComponent;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopComponentTest extends IntegrationTestCase
{
    public function testRenderStopsInactiveShopWithException(): void
    {
        $shop = Registry::getConfig()->getActiveShop();
        $originalActiveFlag = $shop->oxshops__oxactive;
        $shop->oxshops__oxactive = new Field(0);

        try {
            oxNew(ShopComponent::class)->render();
            $this->fail('Expected the inactive shop to stop the request');
        } catch (ShopOfflineException) {
            $this->addToAssertionCount(1);
        } finally {
            $shop->oxshops__oxactive = $originalActiveFlag;
        }
    }

    public function testRenderReturnsActiveShop(): void
    {
        $shop = Registry::getConfig()->getActiveShop();

        $this->assertSame($shop, oxNew(ShopComponent::class)->render());
    }
}
