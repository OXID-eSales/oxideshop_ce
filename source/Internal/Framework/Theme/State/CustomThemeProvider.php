<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;

readonly class CustomThemeProvider implements CustomThemeProviderInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeParentProviderInterface $themeParentProvider,
    ) {
    }

    public function hasCustomTheme(int $shopId): bool
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($this->themeHasParent($themeConfiguration->getId(), $shopId)) {
                return true;
            }
        }

        return false;
    }

    public function getCustomThemeId(int $shopId): string
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($this->themeHasParent($themeConfiguration->getId(), $shopId)) {
                return $themeConfiguration->getId();
            }
        }

        throw new CustomThemeNotFoundException();
    }

    private function themeHasParent(string $themeId, int $shopId): bool
    {
        try {
            return $this->themeParentProvider->hasParentTheme($themeId, $shopId);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }
}
