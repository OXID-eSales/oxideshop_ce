<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;

readonly class ActiveThemeDirectoryResolver implements ActiveThemeDirectoryResolverInterface
{
    public function __construct(
        private ThemePathResolverInterface $themePathResolver,
    ) {
    }

    public function getDirectory(ActiveTheme $activeTheme, int $shopId): string
    {
        return $this->getThemeDirectory($activeTheme->getId(), $shopId);
    }

    public function hasParentDirectory(ActiveTheme $activeTheme, int $shopId): bool
    {
        if (!$activeTheme->getInheritance()->hasParentTheme()) {
            return false;
        }

        try {
            $this->getParentDirectory($activeTheme, $shopId);

            return true;
        } catch (ThemeConfigurationNotFoundException) {
            return false;
        }
    }

    public function getParentDirectory(ActiveTheme $activeTheme, int $shopId): string
    {
        return $this->getThemeDirectory($activeTheme->getInheritance()->getParentThemeId(), $shopId);
    }

    private function getThemeDirectory(string $themeId, int $shopId): string
    {
        return $this->themePathResolver->getFullThemePathFromConfiguration($themeId, $shopId) . DIRECTORY_SEPARATOR;
    }
}
