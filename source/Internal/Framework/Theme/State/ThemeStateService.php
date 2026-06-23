<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;

readonly class ThemeStateService implements ThemeStateServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao
    ) {
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        return $this->themeConfigurationDao->exists($themeId, $shopId)
            && $this->themeConfigurationDao->get($themeId, $shopId)->isActivated();
    }

    public function getActiveThemeId(int $shopId): string
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->isActivated()) {
                return $themeConfiguration->getId();
            }
        }

        return '';
    }

    /**
     * @return string[]
     */
    public function getActiveThemeChain(int $shopId): array
    {
        $activeThemeId = $this->getActiveThemeId($shopId);

        if ($activeThemeId === '') {
            return [];
        }

        $chain = [$activeThemeId];
        $currentThemeId = $activeThemeId;

        while ($this->themeConfigurationDao->exists($currentThemeId, $shopId)) {
            $parentThemeId = $this->themeConfigurationDao->get($currentThemeId, $shopId)->getParentTheme();

            if ($parentThemeId === '' || in_array($parentThemeId, $chain, true)) {
                break;
            }

            array_unshift($chain, $parentThemeId);
            $currentThemeId = $parentThemeId;
        }

        return $chain;
    }
}
