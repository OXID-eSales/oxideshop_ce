<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testBuildResponseWithInvalidControllerThrowsRoutingException(): void
    {
        $this->expectException(RoutingException::class);

        $shopControl = oxNew(ShopControl::class);
        $shopControl->buildResponse('nonexistent_controller_key');
    }
}
