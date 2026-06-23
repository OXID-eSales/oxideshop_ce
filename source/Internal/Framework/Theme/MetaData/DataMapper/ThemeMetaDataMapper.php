<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration\Setting;

readonly class ThemeMetaDataMapper implements ThemeMetaDataMapperInterface
{
    public function fromData(array $data): ThemeConfiguration
    {
        $themeConfiguration = new ThemeConfiguration();
        $themeConfiguration->setId((string) $data['id']);

        if (isset($data['version'])) {
            $themeConfiguration->setVersion((string) $data['version']);
        }

        if (isset($data['parentTheme'])) {
            $themeConfiguration->setParentTheme((string) $data['parentTheme']);
        }

        if (isset($data['title'])) {
            $themeConfiguration->setTitle($this->toLanguageMap($data['title']));
        }

        if (isset($data['description'])) {
            $themeConfiguration->setDescription($this->toLanguageMap($data['description']));
        }

        if (isset($data['thumbnail'])) {
            $themeConfiguration->setThumbnail((string) $data['thumbnail']);
        }

        if (isset($data['author'])) {
            $themeConfiguration->setAuthor((string) $data['author']);
        }

        foreach ($data['settings'] ?? [] as $settingData) {
            $themeConfiguration->addSetting($this->mapSetting($settingData));
        }

        return $themeConfiguration;
    }

    private function mapSetting(array $settingData): Setting
    {
        $setting = new Setting();
        $setting
            ->setName($settingData['name'])
            ->setType($settingData['type'] ?? 'str')
            ->setValue($settingData['value'] ?? '');

        if (isset($settingData['group'])) {
            $setting->setGroupName($settingData['group']);
        }

        if (isset($settingData['position'])) {
            $setting->setPositionInGroup((int) $settingData['position']);
        }

        if (isset($settingData['constraints'])) {
            $setting->setConstraints($this->normalizeConstraints($settingData['constraints']));
        }

        return $setting;
    }

    private function toLanguageMap(mixed $value): array
    {
        return is_array($value) ? $value : ['en' => (string) $value];
    }

    /**
     * @return string[]
     */
    private function normalizeConstraints(mixed $constraints): array
    {
        if (is_array($constraints)) {
            return $constraints;
        }

        return $constraints === '' ? [] : explode('|', (string) $constraints);
    }
}
