<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridge;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ActiveModulesDataProviderBridgeTest extends TestCase
{
    public function testGetClassExtensionIfShopConfigurationIsMissing(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $activeModulesDataProvider = $this->createMock(ActiveModulesDataProviderInterface::class);
        $activeModulesDataProvider->method('getClassExtensions')->willThrowException(new ShopConfigurationNotFoundException());

        $bridge = new ActiveModulesDataProviderBridge(
            $activeModulesDataProvider,
            $logger
        );

        $this->assertEquals([], $bridge->getClassExtensions());
    }

    public function testGetClassExtensionCallsOnlyOnce(): void
    {
        $activeModulesDataProvider = $this->createMock(ActiveModulesDataProviderInterface::class);
        $activeModulesDataProvider
            ->expects($this->once())
            ->method('getClassExtensions')
            ->willReturn(['test' => 'test']);

        $bridge = new ActiveModulesDataProviderBridge(
            $activeModulesDataProvider,
            $this->createMock(LoggerInterface::class)
        );

        $this->assertEquals(['test' => 'test'], $bridge->getClassExtensions());
        $this->assertEquals(['test' => 'test'], $bridge->getClassExtensions());
    }
}
