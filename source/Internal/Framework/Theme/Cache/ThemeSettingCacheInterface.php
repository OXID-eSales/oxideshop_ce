<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

interface ThemeSettingCacheInterface
{
    public function put(string $key, array $data): void;

    /**
     * @throws CacheItemNotFoundException
     */
    public function get(string $key): array;
}
