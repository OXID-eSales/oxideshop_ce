<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ThemeSettingService implements ThemeSettingServiceInterface
{
    private array $settingValues = [];

    public function __construct(
        private readonly ContextInterface $context,
        private readonly ThemeConfigurationResolverInterface $themeConfigurationResolver,
        private readonly TagAwareCacheInterface $cache,
        private readonly string $activeThemeId,
        private readonly string $environment,
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

    /** @return array<string, mixed> */
    private function getSettingValues(): array
    {
        try {
            return $this->settingValues[$this->activeThemeId] ??= $this->cache->get(
                $this->getCacheKey(),
                fn (): array => $this->resolveSettingValues()
            );
        } catch (ThemeConfigurationNotFoundException) {
            return [];
        }
    }

    /** @return array<string, mixed> */
    private function resolveSettingValues(): array
    {
        $themeConfiguration = $this->themeConfigurationResolver->resolve(
            $this->activeThemeId,
            $this->context->getCurrentShopId()
        );

        $settingValues = [];
        foreach ($themeConfiguration->getThemeSettings() as $setting) {
            $settingValues[$setting->getName()] = $setting->getValue();
        }

        return $settingValues;
    }

    private function getCacheKey(): string
    {
        return sprintf('theme_settings_%s_%s', $this->environment, $this->activeThemeId);
    }
}
