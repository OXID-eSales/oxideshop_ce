<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ThemeSettingService implements ThemeSettingServiceInterface
{
    public function __construct(
        private ContextInterface $context,
        private ThemeConfigurationResolverInterface $themeConfigurationResolver,
        private ThemeSettingCacheInterface $themeSettingCache,
        private ActiveThemeProviderInterface $activeThemeProvider,
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
        $shopId = $this->context->getCurrentShopId();

        try {
            $themeId = $this->activeThemeProvider->getActiveThemeId($shopId);
        } catch (ActiveThemeNotFoundException) {
            return ['exists' => false, 'value' => null];
        }

        $cacheKey = 'theme-' . $themeId . '-setting-' . $name;

        try {
            return $this->themeSettingCache->get($cacheKey);
        } catch (CacheItemNotFoundException) {
            $setting = $this->themeConfigurationResolver->resolve($themeId, $shopId)->getSettingByName($name);
            $settingData = [
                'exists' => $setting !== null,
                'value' => $setting?->getValue(),
            ];
            $this->themeSettingCache->put($cacheKey, $settingData);

            return $settingData;
        }
    }
}
