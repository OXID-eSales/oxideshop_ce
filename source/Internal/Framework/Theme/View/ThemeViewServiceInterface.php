<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;

interface ThemeViewServiceInterface
{
    /** @throws ThemeNotLoadableException */
    public function getTheme(string $themeId, int $shopId): ThemeView;

    public function hasParentTheme(string $themeId, int $shopId): bool;

    public function getParentTheme(string $themeId, int $shopId): ParentThemeView;
}
