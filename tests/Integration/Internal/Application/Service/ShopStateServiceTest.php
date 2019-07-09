<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application\Service;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\Service\ShopStateService;
use OxidEsales\EshopCommunity\Internal\Application\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ShopStateServiceTest extends TestCase
{
    use ContainerTrait;

    public function testIsLaunchedReturnsTrue()
    {
        $this->assertTrue(
            $this->get(ShopStateServiceInterface::class)->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfUnifiedNamespaceAreNotGenerated()
    {
        $shopStateService = new ShopStateService(
            $this->get(BasicContextInterface::class),
            'fakeNamespace'
        );

        $this->assertFalse(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsTrueIfUnifiedNamespaceAreGenerated()
    {
        $shopStateService = new ShopStateService(
            $this->get(BasicContextInterface::class),
            Registry::class
        );

        $this->assertTrue(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfConfigFileDoesNotExist()
    {
        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context
            ->method('getConfigFilePath')
            ->willReturn('nonExistentFilePath');

        $shopStateService = new ShopStateService(
            $context,
            Registry::class
        );

        $this->assertFalse(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsTrueIfConfigFileExists()
    {
        $shopStateService = new ShopStateService(
            $this->get(BasicContextInterface::class),
            Registry::class
        );

        $this->assertTrue(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfConfigIsWrong()
    {
        $context = $this->getMockBuilder(BasicContextInterface::class)->getMock();
        $context
            ->method('getConfigFilePath')
            ->willReturn(__DIR__ . '/Fixtures/wrong_config.inc.php');

        $shopStateService = new ShopStateService(
            $context,
            Registry::class
        );

        $this->assertFalse(
            $shopStateService->isLaunched()
        );
    }

    public function testIsLaunchedReturnsFalseIfConfigTableDoesNotExist()
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
