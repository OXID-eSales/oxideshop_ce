<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ThemeParentNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

interface ActiveThemeDirectoryResolverInterface
{
    /** @throws ThemeConfigurationNotFoundException */
    public function getActiveThemeDirectory(ActiveTheme $activeTheme, int $shopId): string;

    public function hasParentThemeDirectory(ActiveTheme $activeTheme, int $shopId): bool;

    /**
     * @throws ThemeParentNotFoundException
     * @throws ThemeConfigurationNotFoundException
     */
    public function getParentThemeDirectory(ActiveTheme $activeTheme, int $shopId): string;
}
