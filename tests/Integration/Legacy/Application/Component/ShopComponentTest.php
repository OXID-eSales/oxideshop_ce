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
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShopComponentTest extends IntegrationTestCase
{
    public function testRenderStopsInactiveShopWithNotFoundResponse(): void
    {
        $shop = Registry::getConfig()->getActiveShop();
        $originalActiveFlag = $shop->oxshops__oxactive;
        $shop->oxshops__oxactive = new Field(0);

        try {
            oxNew(ShopComponent::class)->render();
            $this->fail('Expected the request to stop with a response signal');
        } catch (ResponseReady $signal) {
            $response = $signal->getResponse();
            $this->assertInstanceOf(OfflinePageResponse::class, $response);
            $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertNotSame('', $response->getContent());
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
