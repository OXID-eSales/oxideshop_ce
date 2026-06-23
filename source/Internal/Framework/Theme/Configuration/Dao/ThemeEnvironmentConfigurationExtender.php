<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfiguration\ThemeSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration\Setting;

readonly class ThemeEnvironmentConfigurationExtender implements ThemeConfigurationExtenderInterface
{
    public function __construct(
        private ThemeEnvironmentConfigurationDaoInterface $themeEnvironmentConfigurationDao
    ) {
    }

    public function extend(ThemeConfiguration $themeConfiguration, int $shopId): ThemeConfiguration
    {
        $environmentData = $this->themeEnvironmentConfigurationDao->get($themeConfiguration->getId(), $shopId);

        if (isset($environmentData[ThemeSettingsDataMapper::MAPPING_KEY])) {
            foreach ($environmentData[ThemeSettingsDataMapper::MAPPING_KEY] as $settingId => $environmentSetting) {
                if ($themeConfiguration->hasSetting($settingId)) {
                    $this->mergeEnvironmentSetting($themeConfiguration->getSetting($settingId), $environmentSetting);
                }
            }
        }

        return $themeConfiguration;
    }

    private function mergeEnvironmentSetting(Setting $originalSetting, array $environmentSetting): void
    {
        if (isset($environmentSetting['value'])) {
            $originalSetting->setValue($environmentSetting['value']);
        }

        if (isset($environmentSetting['group'])) {
            $originalSetting->setGroupName($environmentSetting['group']);
        }

        if (isset($environmentSetting['position'])) {
            $originalSetting->setPositionInGroup((int) $environmentSetting['position']);
        }

        if (isset($environmentSetting['constraints'])) {
            $originalSetting->setConstraints($environmentSetting['constraints']);
        }
    }
}
