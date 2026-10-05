<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

interface ActiveThemeCacheInterface
{
    public function put(int $shopId, ActiveTheme $activeTheme): void;

    /**
     * @throws CacheItemNotFoundException
     */
    public function get(int $shopId): ActiveTheme;
}
