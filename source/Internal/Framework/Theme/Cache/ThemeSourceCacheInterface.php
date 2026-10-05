<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

interface ThemeSourceCacheInterface
{
    public function put(string $themeId, int $shopId, string $source): void;

    /**
     * @throws CacheItemNotFoundException
     */
    public function get(string $themeId, int $shopId): string;
}
