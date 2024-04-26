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
use OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('exclude_from_compilation')]
final class ModuleVariablesLocatorTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        ModuleVariablesLocator::resetModuleVariables();
        parent::tearDown();
    }

    public function testLocatorReturnsEmptyModuleChainIfSubshopDoesNotExist(): void
    {
        $randomShopId = time();

        $this->switchShop($randomShopId);

        $locator = $this->getModuleVariablesLocator();

        ModuleVariablesLocator::resetModuleVariables();
        $this->assertEquals(
            [],
            $locator->getModuleVariable('aModules')
        );
    }

    public function testLocatorReturnsChainFromCache(): void
    {
        $this->switchShop(1);

        $testChain = ['shopClass' => 'extension'];

        $this->putChainToCache($testChain);

        $this->assertEquals(
            $testChain,
            $this->getModuleVariablesLocator()->getModuleVariable('aModules')
        );
    }

    private function getModuleVariablesLocator(): ModuleVariablesLocator
    {
        $shopIdCalculator = new ShopIdCalculator(new FileCache());
        $subShopSpecificCache = new SubShopSpecificFileCache($shopIdCalculator);
        return new ModuleVariablesLocator($subShopSpecificCache, $shopIdCalculator);
    }

    private function switchShop(int $shopId): void
    {
        $_POST['shp'] = $shopId;
    }

    private function putChainToCache(array $chain): void
    {
        $shopId = 1;
        $cacheKey = 'module_class_extensions';

        ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleCacheServiceInterface::class)
            ->put(
                $cacheKey,
                $shopId,
                $chain
            );
    }
}
