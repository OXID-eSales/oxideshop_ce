<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setting\Setting;

/**
 * @internal
 */
class ModuleSettingsDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'moduleSettings';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasModuleSettings()) {
            $data[self::MAPPING_KEY] = $this->mapSettingsToData($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->mapSettingsFromData($moduleConfiguration, $data);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return array
     */
    private function mapSettingsToData(ModuleConfiguration $configuration): array
    {
        $data = [];

        foreach ($configuration->getModuleSettings() as $index => $setting) {
            if ($setting->getGroupName()) {
                $data[$index]['group'] = $setting->getGroupName();
            }

            if ($setting->getName()) {
                $data[$index]['name'] = $setting->getName();
            }

            if ($setting->getType()) {
                $data[$index]['type'] = $setting->getType();
            }

            $data[$index]['value'] = $setting->getValue();

            if (!empty($setting->getConstraints())) {
                $data[$index]['constraints'] = $setting->getConstraints();
            }

            if ($setting->getPositionInGroup() > 0) {
                $data[$index]['position'] = $setting->getPositionInGroup();
            }
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param array $data
     * @return ModuleConfiguration
     */
    private function mapSettingsFromData(ModuleConfiguration $configuration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            foreach ($data[self::MAPPING_KEY] as $settingData) {
                $setting = new Setting();

                $setting->setType($settingData['type']);

                if (isset($settingData['value'])) {
                    $setting->setValue($settingData['value']);
                }

                if (!isset($settingData['value'])) {
                    $setting->setValue('');
                }

                if (isset($settingData['name'])) {
                    $setting->setName($settingData['name']);
                }

                if (isset($settingData['group'])) {
                    $setting->setGroupName($settingData['group']);
                }

                if (isset($settingData['position'])) {
                    $setting->setPositionInGroup($settingData['position']);
                }

                if (isset($settingData['constraints'])) {
                    $setting->setConstraints($settingData['constraints']);
                }

                $configuration->addModuleSetting($setting);
            }
        }

        return $configuration;
    }
}
