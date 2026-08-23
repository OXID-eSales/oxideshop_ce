<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;

interface ThemePathResolverInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     */
    public function getFullThemePathFromConfiguration(string $themeId, int $shopId): string;
}
