<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Common\Exception\UnsupportedMethodException;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ModuleConfigurationToShopConfigurationDataMapper implements ModuleConfigurationDataMapperInterface
{
    /**
     * @param ModuleConfiguration $configuration
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        $data['aModulePaths'] = [$configuration->getId() => $configuration->getPath()];
        $data['aModuleVersions'] = [$configuration->getId() => $configuration->getVersion()];

        $data = array_merge($data, $this->getMappedSettings($configuration));

        return $data;
    }

    /**
     * @param array $data
     *
     * @throws UnsupportedMethodException
     */
    public function fromData(array $data): ModuleConfiguration
    {
        throw new UnsupportedMethodException('Mapping from data is not supported by this data mapper.');
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return array
     */
    public function getMappedSettings(ModuleConfiguration $configuration): array
    {
        $data = [];

        foreach ($this->getSettingMap() as $moduleSettingName => $shopConfigurationSettingName) {
            if ($configuration->hasSetting($moduleSettingName)) {
                $setting = $configuration->getModuleSetting($moduleSettingName);

                $data[$shopConfigurationSettingName] = [
                    $configuration->getId() => $setting->getValue()
                ];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getSettingMap(): array
    {
        return [
            'controllers'               => 'aModuleControllers',
            'events'                    => 'aModuleEvents',
            'templates'                 => 'aModuleTemplates',
            'extend'                    => 'aModuleExtensions',
            'smartyPluginDirectories'   => 'moduleSmartyPluginDirectories',

        ];
    }
}
