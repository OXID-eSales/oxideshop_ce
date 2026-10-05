<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;

readonly class ThemeSourceCache implements ThemeSourceCacheInterface
{
    private const CACHE_KEY_PREFIX = 'theme-source-';

    public function __construct(private TagAwareAdapterFactoryInterface $cacheFactory)
    {
    }

    public function put(string $themeId, int $shopId, string $source): void
    {
        $cache = $this->cacheFactory->create($shopId);
        $cacheItem = $cache->getItem(self::CACHE_KEY_PREFIX . $themeId);
        $cacheItem->set($source);
        $cache->save($cacheItem);
    }

    public function get(string $themeId, int $shopId): string
    {
        $cacheItem = $this->cacheFactory->create($shopId)->getItem(self::CACHE_KEY_PREFIX . $themeId);

        if (!$cacheItem->isHit()) {
            throw new CacheItemNotFoundException("Source of theme '$themeId' in shop $shopId is not cached.");
        }

        return $cacheItem->get();
    }
}
