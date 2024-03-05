<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateService;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ShopStateServiceTest extends TestCase
{
    use ContainerTrait;

    public function testIsLaunchedReturnsTrue(): void
    {
        $this->assertTrue(
            $this->get(ShopStateServiceInterface::class)->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfUnifiedNamespaceAreNotGenerated(): void
    {
        $shopStateService = new ShopStateService(
            $this->get(BasicContextInterface::class),
            'fakeNamespace'
        );

        $this->assertFalse(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsTrueIfUnifiedNamespaceAreGenerated(): void
    {
        $shopStateService = new ShopStateService(
            $this->get(BasicContextInterface::class),
            Registry::class
        );

        $this->assertTrue(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfConfigTableDoesNotExist(): void
    {
        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context
            ->method('getConfigTableName')
            ->willReturn('nonExistentTable');

        $shopStateService = new ShopStateService(
            $context,
            Registry::class
        );

        $this->assertFalse(
            $shopStateService->isLaunched()
        );
    }
}
