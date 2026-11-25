<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\ShopComponent;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopComponentTest extends IntegrationTestCase
{
    private Field $activeFlag;

    public function setUp(): void
    {
        parent::setUp();

        $this->activeFlag = Registry::getConfig()->getActiveShop()->oxshops__oxactive;
    }

    public function tearDown(): void
    {
        Registry::getConfig()->getActiveShop()->oxshops__oxactive = $this->activeFlag;

        parent::tearDown();
    }

    public function testRenderStopsInactiveShopWithException(): void
    {
        Registry::getConfig()->getActiveShop()->oxshops__oxactive = new Field(0);

        $this->expectException(ShopOfflineException::class);

        oxNew(ShopComponent::class)->render();
    }
}
