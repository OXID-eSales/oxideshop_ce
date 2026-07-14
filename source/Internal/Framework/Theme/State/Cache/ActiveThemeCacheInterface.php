<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

interface ActiveThemeCacheInterface
{
    public function putThemeId(int $shopId, string $themeId): void;

    public function getThemeId(int $shopId): string;

    public function hasThemeId(int $shopId): bool;

    public function putTheme(int $shopId, ActiveTheme $activeTheme): void;

    public function getTheme(int $shopId): ActiveTheme;

    public function hasTheme(int $shopId): bool;

    public function evict(int $shopId): void;
}
