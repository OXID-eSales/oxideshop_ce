<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ShopModuleSettingsMergingService implements ShopModuleSettingsMergingServiceInterface
{
    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfigurationToMerge
     *
     * @return ModuleConfiguration
     */
    public function merge(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfigurationToMerge
    ): ModuleConfiguration {
        if ($shopConfiguration->hasModuleConfiguration($moduleConfigurationToMerge->getId())) {
            $existingModuleConfiguration = $shopConfiguration->getModuleConfiguration($moduleConfigurationToMerge->getId());
            if ($existingModuleConfiguration->hasSetting(ModuleSetting::SHOP_MODULE_SETTING) &&
                $moduleConfigurationToMerge->hasSetting(ModuleSetting::SHOP_MODULE_SETTING)
            ) {
                $existingModuleSettings = $existingModuleConfiguration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);
                $moduleSettingsToMerge = $moduleConfigurationToMerge->getSetting(ModuleSetting::SHOP_MODULE_SETTING);
                $mergedModuleSettings = $this->mergeModuleSettings(
                    $existingModuleSettings->getValue(),
                    $moduleSettingsToMerge->getValue()
                );
                $moduleConfigurationToMerge->addSetting($mergedModuleSettings);
            }
        }
        return $moduleConfigurationToMerge;
    }

    /**
     * @param array $existingSettings
     * @param array $settingsToMerge
     *
     * @return ModuleSetting
     */
    private function mergeModuleSettings(array $existingSettings, array $settingsToMerge): ModuleSetting
    {
        foreach ($settingsToMerge as &$settingToMerge) {
            foreach ($existingSettings as $existingSetting) {
                if ($this->shouldMerge($existingSetting, $settingToMerge)) {
                    $settingToMerge['value'] = $existingSetting['value'];
                }
            }
        }
        return new ModuleSetting(ModuleSetting::SHOP_MODULE_SETTING, $settingsToMerge);
    }

    /**
     * @param array $existingSetting
     * @param array $settingToMerge
     * @return bool
     */
    private function shouldMerge(array $existingSetting, array $settingToMerge): bool
    {
        $shouldMerge = isset($existingSetting['value']) &&
            $existingSetting['name'] === $settingToMerge['name'] &&
            $existingSetting['type'] === $settingToMerge['type'];
        if ($shouldMerge === true && isset($settingToMerge['constraints']) && ($settingToMerge['type'] === 'select')) {
            $resultPosition = array_search($existingSetting['value'], $settingToMerge['constraints'], true);
            $shouldMerge = $resultPosition !== false;
        }
        return $shouldMerge;
    }
}
