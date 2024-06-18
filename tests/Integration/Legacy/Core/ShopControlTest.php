<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class ShopControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testStartWithExceptionNoDebug()
    {
        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('fnc', "testFnc");

        $this->createContainer();
        $this->container->setParameter('oxid_debug_mode', false);
        $this->container->compile();
        $this->attachContainerToContainerFactory();

        $utils = $this->createMock(Utils::class);
        $utils->expects($this->once())
            ->method('redirect')
            ->with(Registry::getConfig()->getShopHomeUrl() . 'cl=start');
        Registry::set(Utils::class, $utils);

        $shopControl = $this->getMockBuilder(ShopControl::class)
            ->onlyMethods(['isAdmin', 'runOnce'])
            ->getMock();
        $shopControl->method('isAdmin')->willReturn(false);
        $shopControl->method('runOnce')->willThrowException(new SystemComponentException());

        $shopControl->start();
    }

    private function setRequestParameter(string $key, $value): void
    {
        $_POST[$key] = $value;
    }
}