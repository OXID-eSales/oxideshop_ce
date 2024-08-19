<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testStartWithExceptionAndDisabledDebugModeWillRedirect(): void
    {
        $this->createContainer();
        $this->container->setParameter('oxid_debug_mode', false);
        $this->container->compile();
        $this->attachContainerToContainerFactory();
        $shopControl = $this->getMockBuilder(ShopControl::class)
            ->onlyMethods(['isAdmin', 'runOnce'])
            ->getMock();
        $shopControl->method('isAdmin')
            ->willReturn(false);
        $shopControl->method('runOnce')
            ->willThrowException(new SystemComponentException());
        $utils = $this->createMock(Utils::class);
        Registry::set(Utils::class, $utils);

        $utils->expects($this->once())
            ->method('redirect')
            ->with(Registry::getConfig()->getShopHomeUrl() . 'cl=start');

        $shopControl->start();
    }
}
