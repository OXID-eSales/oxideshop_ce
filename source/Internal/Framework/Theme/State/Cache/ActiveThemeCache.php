<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

class ActiveThemeCache implements ActiveThemeCacheInterface
{
    private array $themeIds = [];
    private array $themes = [];

    public function putThemeId(int $shopId, string $themeId): void
    {
        $this->themeIds[$shopId] = $themeId;
    }

    public function getThemeId(int $shopId): string
    {
        return $this->themeIds[$shopId];
    }

    public function hasThemeId(int $shopId): bool
    {
        return isset($this->themeIds[$shopId]);
    }

    public function putTheme(int $shopId, ActiveTheme $activeTheme): void
    {
        $this->themes[$shopId] = $activeTheme;
    }

    public function getTheme(int $shopId): ActiveTheme
    {
        return $this->themes[$shopId];
    }

    public function hasTheme(int $shopId): bool
    {
        return isset($this->themes[$shopId]);
    }

    public function evict(int $shopId): void
    {
        unset($this->themeIds[$shopId], $this->themes[$shopId]);
    }
}
