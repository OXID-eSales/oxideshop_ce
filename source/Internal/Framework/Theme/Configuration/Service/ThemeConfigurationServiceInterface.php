<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\EnvironmentOverriddenSettingException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

interface ThemeConfigurationServiceInterface
{
    /** @throws ThemeConfigurationNotFoundException */
    public function getConfiguration(string $themeId): ThemeConfiguration;

    /**
     * @throws ActiveThemeNotFoundException
     * @throws ThemeConfigurationNotFoundException
     */
    public function getActiveConfiguration(): ThemeConfiguration;

    /** @return array<string, mixed> */
    public function getEnvironmentSettingValues(string $themeId): array;

    /**
     * @param array<string, mixed> $settingValues
     * @throws EnvironmentOverriddenSettingException
     */
    public function updateSettings(ThemeConfiguration $configuration, array $settingValues): void;
}
