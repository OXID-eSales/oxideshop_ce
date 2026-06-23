<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration\Setting;

readonly class ThemeSettingsDataMapper implements ThemeConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'themeSettings';

    public function toData(ThemeConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasSettings()) {
            $data[self::MAPPING_KEY] = $this->mapSettingsToData($configuration);
        }

        return $data;
    }

    public function fromData(ThemeConfiguration $themeConfiguration, array $data): ThemeConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->mapSettingsFromData($themeConfiguration, $data);
        }

        return $themeConfiguration;
    }

    private function mapSettingsToData(ThemeConfiguration $configuration): array
    {
        $data = [];

        foreach ($configuration->getSettings() as $setting) {
            $data[$setting->getName()]['value'] = $setting->getValue();
        }

        return $data;
    }

    private function mapSettingsFromData(ThemeConfiguration $configuration, array $data): void
    {
        foreach ($data[self::MAPPING_KEY] as $name => $settingData) {
            $setting = new Setting();
            $setting
                ->setName($name)
                ->setType($settingData['type'])
                ->setValue($settingData['value'] ?? '');

            if (isset($settingData['group'])) {
                $setting->setGroupName($settingData['group']);
            }

            if (isset($settingData['position'])) {
                $setting->setPositionInGroup((int) $settingData['position']);
            }

            if (isset($settingData['constraints'])) {
                $setting->setConstraints($settingData['constraints']);
            }

            $configuration->addSetting($setting);
        }
    }
}
