<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;

class SettingsMergingService implements SettingsMergingServiceInterface
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
            if (!empty($existingModuleConfiguration->getModuleSettings()) &&
                !empty($moduleConfigurationToMerge->getModuleSettings())
            ) {
                $mergedModuleSettings = $this->mergeModuleSettings(
                    $existingModuleConfiguration->getModuleSettings(),
                    $moduleConfigurationToMerge->getModuleSettings()
                );
                $moduleConfigurationToMerge->setModuleSettings($mergedModuleSettings);
            }
        }
        return $moduleConfigurationToMerge;
    }

    /**
     * @param Setting[] $existingSettings
     * @param Setting[] $settingsToMerge
     *
     * @return Setting[]
     */
    private function mergeModuleSettings(array $existingSettings, array $settingsToMerge): array
    {
        foreach ($settingsToMerge as &$settingToMerge) {
            foreach ($existingSettings as $existingSetting) {
                if ($this->shouldMerge($existingSetting, $settingToMerge)) {
                    $settingToMerge->setValue($existingSetting->getValue());
                }
            }
        }

        return $settingsToMerge;
    }

    /**
     * @param Setting $existingSetting
     * @param Setting $settingToMerge
     * @return bool
     */
    private function shouldMerge(Setting $existingSetting, Setting $settingToMerge): bool
    {
        $shouldMerge = $existingSetting->getValue() !== null &&
            $existingSetting->getName() === $settingToMerge->getName() &&
            $existingSetting->getType() === $settingToMerge->getType();

        if ($shouldMerge === true
            && !empty($settingToMerge->getConstraints())
            && ($settingToMerge->getType() === 'select')) {
            $resultPosition = array_search($existingSetting->getValue(), $settingToMerge->getConstraints(), true);
            $shouldMerge = $resultPosition !== false;
        }

        return $shouldMerge;
    }
}
