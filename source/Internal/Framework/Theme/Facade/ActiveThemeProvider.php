<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

class ActiveThemeProvider implements ActiveThemeProviderInterface
{
    private array $activeThemeIds = [];

    /** @var array<int, ActiveTheme> */
    private array $activeThemes = [];

    public function __construct(
        private readonly ThemeConfigurationDaoInterface $themeConfigurationDao,
        private readonly ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private readonly ThemeSettingCacheInterface $themeSettingCache,
    ) {
    }

    public function getActiveThemeId(int $shopId): string
    {
        if (isset($this->activeThemeIds[$shopId])) {
            return $this->activeThemeIds[$shopId];
        }

        $cacheKey = 'theme-active-' . $shopId;

        try {
            return $this->activeThemeIds[$shopId] = $this->themeSettingCache->get($cacheKey)['value'];
        } catch (CacheItemNotFoundException) {
            foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
                if ($themeConfiguration->isActivated()) {
                    $themeId = $themeConfiguration->getId();
                    $this->themeSettingCache->put($cacheKey, ['value' => $themeId]);

                    return $this->activeThemeIds[$shopId] = $themeId;
                }
            }

            throw new ActiveThemeNotFoundException();
        }
    }

    public function getActiveTheme(int $shopId): ActiveTheme
    {
        if (isset($this->activeThemes[$shopId])) {
            return $this->activeThemes[$shopId];
        }

        $activeThemeId = $this->getActiveThemeId($shopId);

        return $this->activeThemes[$shopId] = new ActiveTheme(
            $activeThemeId,
            $this->themeMetaDataByIdProvider->getById($activeThemeId, $shopId)->getParentTheme(),
        );
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        $themeConfigurations = $this->themeConfigurationDao->getAll($shopId);

        return isset($themeConfigurations[$themeId]) && $themeConfigurations[$themeId]->isActivated();
    }
}
