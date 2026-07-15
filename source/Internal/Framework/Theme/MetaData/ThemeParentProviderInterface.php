<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;

interface ThemeParentProviderInterface
{
    public function hasParentTheme(string $themeId, int $shopId): bool;

    /** @throws ParentThemeNotFoundException */
    public function getParentThemeId(string $themeId, int $shopId): string;
}
