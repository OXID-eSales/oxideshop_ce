<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use Psr\Cache\CacheItemPoolInterface;

readonly class ThemeSettingCache implements ThemeSettingCacheInterface
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function put(string $key, array $data): void
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($data);
        $this->cache->save($cacheItem);
    }

    /**
     * @throws CacheItemNotFoundException
     */
    public function get(string $key): array
    {
        $cacheItem = $this->cache->getItem($key);

        if (!$cacheItem->isHit()) {
            throw new CacheItemNotFoundException("Cache item with key '$key' not found.");
        }

        return $cacheItem->get();
    }
}
