<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache\ActiveThemeCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

readonly class ThemeStateService implements ThemeStateServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeInheritanceResolverInterface $themeInheritanceResolver,
        private ActiveThemeCacheInterface $activeThemeCache,
    ) {
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        return $this->themeConfigurationDao->exists($themeId, $shopId)
            && $this->themeConfigurationDao->get($themeId, $shopId)->isActivated();
    }

    public function getActiveThemeId(int $shopId): string
    {
        if ($this->activeThemeCache->hasThemeId($shopId)) {
            return $this->activeThemeCache->getThemeId($shopId);
        }

        $themeId = $this->findActiveThemeId($shopId);
        $this->activeThemeCache->putThemeId($shopId, $themeId);

        return $themeId;
    }

    public function getActiveTheme(int $shopId): ActiveTheme
    {
        if ($this->activeThemeCache->hasTheme($shopId)) {
            return $this->activeThemeCache->getTheme($shopId);
        }

        $activeTheme = new ActiveTheme(
            $this->themeInheritanceResolver->resolve($this->getActiveThemeId($shopId), $shopId)
        );
        $this->activeThemeCache->putTheme($shopId, $activeTheme);

        return $activeTheme;
    }

    private function findActiveThemeId(int $shopId): string
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->isActivated()) {
                return $themeConfiguration->getId();
            }
        }

        throw new ActiveThemeNotFoundException();
    }
}
