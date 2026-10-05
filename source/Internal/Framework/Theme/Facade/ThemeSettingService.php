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
        return array_key_exists($name, $this->getSettingValues());
    }

    private function getValue(string $name): mixed
    {
        $settingValues = $this->getSettingValues();

        if (!array_key_exists($name, $settingValues)) {
            throw new ThemeSettingNotFoundException(
                sprintf(
                    "Setting '%s' not found in shop %d",
                    $name,
                    $this->context->getCurrentShopId()
                )
            );
        }

        return $settingValues[$name];
    }

    /**
     * @return array<string, mixed>
     */
    private function getSettingValues(): array
    {
        $shopId = $this->context->getCurrentShopId();

        try {
            $themeId = $this->activeThemeProvider->getActiveThemeId($shopId);
        } catch (ActiveThemeNotFoundException) {
            return [];
        }

        $cacheKey = 'theme-' . $themeId . '-settings';

        try {
            return $this->themeSettingCache->get($cacheKey);
        } catch (CacheItemNotFoundException) {
            $settingValues = $this->resolveSettingValues($themeId, $shopId);
            $this->themeSettingCache->put($cacheKey, $settingValues);

            return $settingValues;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveSettingValues(string $themeId, int $shopId): array
    {
        $settingValues = [];

        foreach ($this->themeConfigurationResolver->resolve($themeId, $shopId)->getThemeSettings() as $setting) {
            $settingValues[$setting->getName()] = $setting->getValue();
        }

        return $settingValues;
    }
}
