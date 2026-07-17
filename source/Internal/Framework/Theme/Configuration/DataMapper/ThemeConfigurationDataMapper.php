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
            'title'     => $configuration->getTitle(),
        ];

        foreach ($configuration->getThemeSettings() as $setting) {
            $data['themeSettings'][$setting->getName()] = $this->mapSettingToData($setting);
        }

        return $data;
    }

    public function fromData(array $data): ThemeConfiguration
    {
        $configuration = (new ThemeConfiguration())
            ->setTitle($data['title'] ?? '')
            ->setSource($data['source'] ?? '')
            ->setActivated($data['activated'] ?? false);

        foreach ($data['themeSettings'] ?? [] as $name => $settingData) {
            $configuration->addThemeSetting($this->mapSettingFromData((string) $name, $settingData));
        }

        return $configuration;
    }

    private function mapSettingToData(Setting $setting): array
    {
        $data = [
            'type'  => $setting->getType(),
            'value' => $setting->getValue(),
        ];

        if ($setting->getGroupName() !== '') {
            $data['group'] = $setting->getGroupName();
        }
        if ($setting->getPositionInGroup() > 0) {
            $data['position'] = $setting->getPositionInGroup();
        }
        if ($setting->getConstraints() !== []) {
            $data['constraints'] = $setting->getConstraints();
        }

        return $data;
    }

    private function mapSettingFromData(string $name, array $settingData): Setting
    {
        return (new Setting())
            ->setName($name)
            ->setType($settingData['type'] ?? '')
            ->setValue($settingData['value'] ?? null)
            ->setGroupName($settingData['group'] ?? '')
            ->setPositionInGroup((int) ($settingData['position'] ?? 0))
            ->setConstraints($settingData['constraints'] ?? []);
    }
}
