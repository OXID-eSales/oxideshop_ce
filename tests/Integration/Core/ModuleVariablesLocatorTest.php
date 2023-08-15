<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ModuleVariablesLocatorTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ModuleVariablesLocator::resetModuleVariables();
    }

    public function tearDown(): void
    {
        ModuleVariablesLocator::resetModuleVariables();
        parent::tearDown();
    }

    public function testShopIdCalculatorWithWrongId(): void
    {
        $shopIdTest = 9999;
        $expectedResult = [];
        $result = $this->getVariableByShopId($shopIdTest);
        $this->assertEquals($expectedResult, $result);
    }

    private function getVariableByShopId(int $shopId): array|string
    {
        $fileCacheMock = $this->createMock(FileCache::class);
        $shopIdCalculatorMock = $this->createMock(ShopIdCalculator::class);
        $shopIdCalculatorMock->method('getShopId')->willReturn($shopId);
        $moduleVariablesLocatorClass = new ModuleVariablesLocator($fileCacheMock, $shopIdCalculatorMock);
        return $moduleVariablesLocatorClass->getModuleVariable('aModules');
    }
}
