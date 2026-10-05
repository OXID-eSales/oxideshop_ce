<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ActiveThemeCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

readonly class ActiveThemeProvider implements ActiveThemeProviderInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private ActiveThemeCacheInterface $activeThemeCache,
    ) {
    }

    public function getActiveThemeId(int $shopId): string
    {
        try {
            return $this->getActiveTheme($shopId)->getId();
        } catch (ThemeNotLoadableException) {
            return $this->findActivatedThemeId($shopId);
        }
    }

    public function getActiveTheme(int $shopId): ActiveTheme
    {
        try {
            return $this->activeThemeCache->get($shopId);
        } catch (CacheItemNotFoundException) {
            $activeTheme = $this->readActiveTheme($shopId);
            $this->activeThemeCache->put($shopId, $activeTheme);

            return $activeTheme;
        }
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        try {
            return $this->getActiveThemeId($shopId) === $themeId;
        } catch (ActiveThemeNotFoundException) {
            return false;
        }
    }

    private function readActiveTheme(int $shopId): ActiveTheme
    {
        $activeThemeId = $this->findActivatedThemeId($shopId);

        return new ActiveTheme(
            $activeThemeId,
            $this->themeMetaDataByIdProvider->getById($activeThemeId, $shopId)->getParentTheme(),
        );
    }

    private function findActivatedThemeId(int $shopId): string
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->isActivated()) {
                return $themeConfiguration->getId();
            }
        }

        throw new ActiveThemeNotFoundException();
    }
}
