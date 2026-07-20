<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

readonly class ThemeConfigurationDataMapper implements ThemeConfigurationDataMapperInterface
{
    public function toData(ThemeConfiguration $configuration): array
    {
        $data = [
            'source'    => $configuration->getSource(),
            'activated' => $configuration->isActivated(),
        ];

        if ($configuration->hasThemeSettings()) {
            $data['themeSettings'] = $this->mapSettingsToData($configuration->getThemeSettings());
        }

        return $data;
    }

    public function fromData(array $data): ThemeConfiguration
    {
        $configuration = new ThemeConfiguration();
        $configuration
            ->setSource($data['source'] ?? '')
            ->setActivated($data['activated'] ?? false);

        foreach ($data['themeSettings'] ?? [] as $name => $settingData) {
            $setting = new Setting();
            $setting->setName((string) $name);
            $setting->setType($settingData['type'] ?? '');
            $setting->setValue($settingData['value'] ?? null);
            $setting->setGroupName($settingData['group'] ?? '');
            $setting->setPositionInGroup((int) ($settingData['position'] ?? 0));
            $setting->setConstraints($settingData['constraints'] ?? []);

            $configuration->addThemeSetting($setting);
        }

        return $configuration;
    }

    /** @param Setting[] $settings */
    private function mapSettingsToData(array $settings): array
    {
        $data = [];

        foreach ($settings as $setting) {
            $entry = [
                'type'  => $setting->getType(),
                'value' => $setting->getValue(),
            ];

            if ($setting->getGroupName()) {
                $entry['group'] = $setting->getGroupName();
            }
            if ($setting->getPositionInGroup() > 0) {
                $entry['position'] = $setting->getPositionInGroup();
            }
            if (!empty($setting->getConstraints())) {
                $entry['constraints'] = $setting->getConstraints();
            }

            $data[$setting->getName()] = $entry;
        }

        return $data;
    }
}
