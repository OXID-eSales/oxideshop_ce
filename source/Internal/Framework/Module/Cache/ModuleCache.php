<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use Psr\Cache\CacheItemPoolInterface;

class ModuleCache implements ModuleCacheInterface
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public function deleteItem(string $key): void
    {
        $this->cache->deleteItem($key);
    }

    public function put(string $key, array $data): void
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($data);
        $this->cache->save($cacheItem);
    }

    /**
     * @throws CacheNotFoundException
     */
    public function get(string $key): array
    {
        $cacheItem = $this->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            throw new CacheNotFoundException("Cache with key '$key' not found.");
        }

        return $cacheItem->get();
    }

    public function exists(string $key): bool
    {
        return $this->cache->getItem($key)->isHit();
    }
}
