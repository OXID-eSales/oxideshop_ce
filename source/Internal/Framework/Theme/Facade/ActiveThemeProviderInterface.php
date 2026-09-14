<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

interface ActiveThemeProviderInterface
{
    /**
     * @throws ActiveThemeNotFoundException
     */
    public function getActiveThemeId(int $shopId): string;

    /**
     * @throws ActiveThemeNotFoundException
     * @throws ThemeNotLoadableException
     */
    public function getActiveTheme(int $shopId): ActiveTheme;

    public function isActive(string $themeId, int $shopId): bool;
}
