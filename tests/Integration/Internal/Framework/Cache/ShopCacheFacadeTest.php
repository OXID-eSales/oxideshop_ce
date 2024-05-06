<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

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

    private function getShopCacheCleaner(): ShopCacheCleanerInterface
    {
        return $this->get(ShopCacheCleanerInterface::class);
    }

    private function getModuleCache(): ModuleCacheInterface
    {
        return $this->get(ModuleCacheInterface::class);
    }
}
