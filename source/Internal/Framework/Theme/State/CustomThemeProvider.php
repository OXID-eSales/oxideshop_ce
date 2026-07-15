<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;

readonly class CustomThemeProvider implements CustomThemeProviderInterface
{
    public function __construct(
        private ThemeStateServiceInterface $themeStateService,
        private ThemeParentProviderInterface $themeParentProvider,
    ) {
    }

    public function hasCustomTheme(int $shopId): bool
    {
        return $this->resolveCustomThemeId($shopId) !== null;
    }

    public function getCustomThemeId(int $shopId): string
    {
        return $this->resolveCustomThemeId($shopId) ?? throw new CustomThemeNotFoundException();
    }

    private function resolveCustomThemeId(int $shopId): ?string
    {
        try {
            $activeThemeId = $this->themeStateService->getActiveThemeId($shopId);
        } catch (ActiveThemeNotFoundException) {
            return null;
        }

        return $this->themeParentProvider->hasParentTheme($activeThemeId, $shopId) ? $activeThemeId : null;
    }
}
