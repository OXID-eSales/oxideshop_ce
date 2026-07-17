<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

interface ThemeStateServiceInterface
{
    public function isActive(string $themeId, int $shopId): bool;

    /** @throws ActiveThemeNotFoundException */
    public function getActiveThemeId(int $shopId): string;

    /** @throws ActiveThemeNotFoundException */
    public function getActiveTheme(int $shopId): ActiveTheme;
}
