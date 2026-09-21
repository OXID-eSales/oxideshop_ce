<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

/**
 * A cache that never stores anything.
 *
 * The bootstrap container runs before the shop is installed, so it has no
 * cache pool to hand out: the pool is built per shop id, and there is no shop
 * yet. Callers still need a collaborator, and a store that always misses is
 * the honest answer — every read falls through to the file, which is exactly
 * what happened before any caching existed.
 */
class NullThemeSettingCache implements ThemeSettingCacheInterface
{
    public function put(string $key, array $data): void
    {
    }

    /**
     * @throws CacheItemNotFoundException always
     */
    public function get(string $key): array
    {
        throw new CacheItemNotFoundException("Cache item with key '$key' not found.");
    }
}
