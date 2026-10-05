<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

readonly class ActiveThemeCache implements ActiveThemeCacheInterface
{
    private const CACHE_KEY = 'active_theme';

    public function __construct(private TagAwareAdapterFactoryInterface $cacheFactory)
    {
    }

    public function put(int $shopId, ActiveTheme $activeTheme): void
    {
        $cache = $this->cacheFactory->create($shopId);
        $cacheItem = $cache->getItem(self::CACHE_KEY);
        $cacheItem->set([
            'id' => $activeTheme->getId(),
            'parentThemeId' => $activeTheme->getParentThemeId(),
        ]);
        $cache->save($cacheItem);
    }

    public function get(int $shopId): ActiveTheme
    {
        $cacheItem = $this->cacheFactory->create($shopId)->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            throw new CacheItemNotFoundException("Active theme of shop $shopId is not cached.");
        }

        $data = $cacheItem->get();

        return new ActiveTheme($data['id'], $data['parentThemeId']);
    }
}
