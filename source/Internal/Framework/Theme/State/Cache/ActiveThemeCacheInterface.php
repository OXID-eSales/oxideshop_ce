<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache;

interface ActiveThemeCacheInterface
{
    public function putThemeId(int $shopId, string $themeId): void;

    public function getThemeId(int $shopId): string;

    public function hasThemeId(int $shopId): bool;

    public function evict(int $shopId): void;
}
