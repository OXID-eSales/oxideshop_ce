<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ModuleCache implements ModuleCacheServiceInterface
{
    private CacheInterface $cache;

    public function __construct(
        private readonly ShopAdapterInterface $shopAdapter,
        private readonly TemplateCacheServiceInterface $templateCacheService,
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    public function invalidate(string $moduleId, int $shopId): void
    {
        $this->templateCacheService->invalidateTemplateCache();
        $this->shopAdapter->invalidateModuleCache($moduleId);
        $this->cache->deleteItem($this->getKey($shopId));
    }

    public function invalidateAll(): void
    {
        $this->templateCacheService->invalidateTemplateCache();
        $this->shopAdapter->invalidateModulesCache();

        $this->cache->clear();
    }

    public function put(string $key, int $shopId, array $data): void
    {
        $cacheModulePathItem = $this->cache->getItem($this->getKey($shopId));
        $cacheModulePathItem->set($data);
        $this->cache->save($cacheModulePathItem);
    }

    /**
     * @throws CacheNotFoundException
     */
    public function get(string $key, int $shopId): array
    {
        $cacheKey = $this->getKey($shopId);
        $cacheModulePathItem = $this->cache->getItem($cacheKey);

        if (!$cacheModulePathItem->isHit()) {
            throw new CacheNotFoundException(
                "Cache with key '$cacheKey' for the shop with id $shopId not found."
            );
        }

        return $cacheModulePathItem->get();
    }

    public function exists(string $key, int $shopId): bool
    {
        return $this->cache->getItem($this->getKey($shopId))->isHit();
    }

    private function getKey(int $shopId): string
    {
        return $shopId . '.modules';
    }
}
