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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ThemeSettingService implements ThemeSettingServiceInterface
{
    public function __construct(
        private ContextInterface $context,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeConfigurationResolverInterface $themeConfigurationResolver,
        private ThemeSettingCacheInterface $themeSettingCache,
        private ThemeStateServiceInterface $themeStateService,
    ) {
    }

    public function getInteger(string $name): int
    {
        return (int) $this->getValue($name);
    }

    public function getFloat(string $name): float
    {
        return (float) $this->getValue($name);
    }

    public function getString(string $name): string
    {
        return (string) $this->getValue($name);
    }

    public function getBoolean(string $name): bool
    {
        return (bool) $this->getValue($name);
    }

    public function getCollection(string $name): array
    {
        return (array) $this->getValue($name);
    }

    public function exists(string $name): bool
    {
        return $this->getSettingData($name)['exists'];
    }

    private function getValue(string $name): mixed
    {
        $settingData = $this->getSettingData($name);

        if (!$settingData['exists']) {
            throw new ThemeSettingNotFoundException(
                sprintf(
                    "Setting '%s' not found in shop %d",
                    $name,
                    $this->context->getCurrentShopId()
                )
            );
        }

        return $settingData['value'];
    }

    private function getSettingData(string $name): array
    {
        try {
            $activeTheme = $this->themeStateService->getActiveTheme($this->context->getCurrentShopId());
        } catch (ActiveThemeNotFoundException) {
            return ['exists' => false, 'value' => null];
        }

        $cacheKey = $this->getCacheKey($activeTheme->getId(), $name);

        try {
            return $this->themeSettingCache->get($cacheKey);
        } catch (CacheItemNotFoundException) {
            $setting = $this->findSetting($activeTheme, $name);
            $settingData = [
                'exists' => $setting !== null,
                'value' => $setting?->getValue(),
            ];
            $this->themeSettingCache->put($cacheKey, $settingData);

            return $settingData;
        }
    }

    private function findSetting(ActiveTheme $activeTheme, string $name): ?Setting
    {
        $shopId = $this->context->getCurrentShopId();
        $inheritance = $activeTheme->getInheritance();

        if ($setting = $this->findSettingForTheme($inheritance->getThemeId(), $shopId, $name)) {
            return $setting;
        }

        return $inheritance->hasParentTheme()
            ? $this->findSettingForTheme($inheritance->getParentThemeId(), $shopId, $name)
            : null;
    }

    private function findSettingForTheme(string $themeId, int $shopId, string $name): ?Setting
    {
        if (!$this->themeConfigurationDao->exists($themeId, $shopId)) {
            return null;
        }

        return $this->themeConfigurationResolver->resolve($themeId, $shopId)->getSettingByName($name);
    }

    private function getCacheKey(string $themeId, string $name): string
    {
        return 'theme-' . $themeId . '-setting-' . $name;
    }
}
