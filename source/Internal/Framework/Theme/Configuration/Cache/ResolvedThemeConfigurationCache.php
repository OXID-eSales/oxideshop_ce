<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

readonly class ResolvedThemeConfigurationCache implements ThemeConfigurationCacheInterface
{
    private const CACHE_KEY_PREFIX = 'resolved-theme-configuration-';

    public function __construct(private TagAwareAdapterFactoryInterface $cacheFactory)
    {
    }

    public function put(int $shopId, ThemeConfiguration $configuration): void
    {
        $cache = $this->cacheFactory->create($shopId);
        $cacheItem = $cache->getItem(self::CACHE_KEY_PREFIX . $configuration->getId());
        $cacheItem->set($configuration);
        $cache->save($cacheItem);
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        return $this->cacheFactory->create($shopId)->getItem(self::CACHE_KEY_PREFIX . $themeId)->get();
    }

    public function exists(string $themeId, int $shopId): bool
    {
        return $this->cacheFactory->create($shopId)->hasItem(self::CACHE_KEY_PREFIX . $themeId);
    }

    public function evict(string $themeId, int $shopId): void
    {
        $this->cacheFactory->create($shopId)->deleteItem(self::CACHE_KEY_PREFIX . $themeId);
    }
}
