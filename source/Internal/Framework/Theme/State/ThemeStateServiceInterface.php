<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

interface ThemeStateServiceInterface
{
    public function isActive(string $themeId, int $shopId): bool;

    public function getActiveThemeId(int $shopId): string;
}
