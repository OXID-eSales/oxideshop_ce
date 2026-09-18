<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeSettingService implements ThemeSettingServiceInterface
{
    private ?ThemeConfiguration $configuration = null;

    private ?string $configurationKey = null;

    public function __construct(
        private readonly ContextInterface $context,
        private readonly ThemeConfigurationResolverInterface $themeConfigurationResolver,
        private readonly ActiveThemeProviderInterface $activeThemeProvider,
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

        $setting = $this->getConfiguration($themeId, $shopId)->getSettingByName($name);

        return [
            'exists' => $setting !== null,
            'value' => $setting?->getValue(),
        ];
    }

    private function getConfiguration(string $themeId, int $shopId): ThemeConfiguration
    {
        $key = $themeId . '@' . $shopId;

        if ($this->configurationKey !== $key) {
            $this->configuration = $this->themeConfigurationResolver->resolve($themeId, $shopId);
            $this->configurationKey = $key;
        }

        return $this->configuration;
    }
}
