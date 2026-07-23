<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

#[Group('cache')]
final class ShopCacheFacadeTest extends TestCase
{
    use ContainerTrait;

    public function testDeleteShopRelatedCache(): void
    {
        $moduleCache = $this->getModuleCache();
        $moduleCache->put('test', ['something']);

        $shopPool = $this->getShopCacheCleaner();
        $shopPool->clear(1);

        $this->assertFalse(
            $moduleCache->exists('test')
        );
    }

    public function testClearMovesThePoolOfTheShopToAnotherNamespace(): void
    {
        $before = $this->getGenerationMarker();

        $this->getShopCacheCleaner()->clear(1);

        $this->assertNotSame($before, $this->getGenerationMarker());
        $this->assertDirectoryExists($this->storeItemAndGetPoolDirectory());
    }

    public function testClearAllMovesThePoolOfTheShopToAnotherNamespace(): void
    {
        $before = $this->getGenerationMarker();

        $this->getShopCacheCleaner()->clearAll();

        $this->assertNotSame($before, $this->getGenerationMarker());
        $this->assertDirectoryExists($this->storeItemAndGetPoolDirectory());
    }

    private function getGenerationMarker(): ?string
    {
        $path = $this->get(BasicContextInterface::class)->getCachePoolGenerationFilePath(1);

        return is_file($path) ? file_get_contents($path) : null;
    }

    private function storeItemAndGetPoolDirectory(): string
    {
        $this->get(TagAwareAdapterFactoryInterface::class)->create(1)->get('some-key', fn (): string => 'value');

        return Path::join(
            $this->get(BasicContextInterface::class)->getCacheDirectory(),
            'pool',
            'cache_items_shop_1_' . $this->getGenerationMarker()
        );
    }

    private function getShopCacheCleaner(): ShopCacheCleanerInterface
    {
        return $this->get(ShopCacheCleanerInterface::class);
    }

    private function getModuleCache(): ModuleCacheInterface
    {
        return $this->get(ModuleCacheInterface::class);
    }
}
