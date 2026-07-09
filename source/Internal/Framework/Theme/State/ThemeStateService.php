<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

readonly class ThemeStateService implements ThemeStateServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
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

        throw new ActiveThemeNotFoundException();
    }
}
