<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ModuleConfigurationMergingService implements ModuleConfigurationMergingServiceInterface
{
    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfigurationToMerge
     *
     * @return ShopConfiguration
     */
    public function merge(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfigurationToMerge
    ): ShopConfiguration {
        $mergedClassExtensionChain = $this->updateClassExtensionChain($shopConfiguration, $moduleConfigurationToMerge);
        $shopConfiguration->setClassExtensionsChain($mergedClassExtensionChain);

        $mergedModuleConfiguration = $this->mergeModuleConfiguration($shopConfiguration, $moduleConfigurationToMerge);
        $shopConfiguration->addModuleConfiguration($mergedModuleConfiguration);

        return $shopConfiguration;
    }

    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return ClassExtensionsChain
     */
    private function updateClassExtensionChain(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfiguration
    ): ClassExtensionsChain {
        $classExtensionChain = $shopConfiguration->getClassExtensionsChain();

        if ($moduleConfiguration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $classExtensionsToMerge = $moduleConfiguration->getSetting(ModuleSetting::CLASS_EXTENSIONS)->getValue();

            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                $existingModuleConfiguration = $shopConfiguration->getModuleConfiguration($moduleConfiguration->getId());

                $classExtensionChain = $this->compareClassExtensionsAndUpdateChain(
                    $existingModuleConfiguration,
                    $classExtensionsToMerge,
                    $classExtensionChain
                );
            } else {
                $classExtensionChain->addExtensions($classExtensionsToMerge);
            }
        }

        return $classExtensionChain;
    }

    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfigurationToMerge
     *
     * @return ModuleConfiguration
     */
    private function mergeModuleConfiguration(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfigurationToMerge
    ) : ModuleConfiguration {
        if ($shopConfiguration->hasModuleConfiguration($moduleConfigurationToMerge->getId())) {
            $existingModuleConfiguration = $shopConfiguration->getModuleConfiguration($moduleConfigurationToMerge->getId());
            if ($existingModuleConfiguration->hasSetting(ModuleSetting::SHOP_MODULE_SETTING) &&
                $moduleConfigurationToMerge->hasSetting(ModuleSetting::SHOP_MODULE_SETTING)
            ) {
                $existingModuleSettings = $existingModuleConfiguration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);
                $moduleSettingsToMerge = $moduleConfigurationToMerge->getSetting(ModuleSetting::SHOP_MODULE_SETTING);
                $mergedModuleSettings = $this->insertValuesOfExistingSettings(
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
    private function insertValuesOfExistingSettings(array $existingSettings, array $settingsToMerge) : ModuleSetting
    {
        foreach ($settingsToMerge as &$settingToMerge) {
            foreach ($existingSettings as $existingSetting) {
                if (isset($existingSetting['value']) &&
                    $existingSetting['name'] === $settingToMerge['name'] &&
                    $existingSetting['type'] === $settingToMerge['type'] &&
                    $this->constraintsAllowThisValue($existingSetting['value'], $settingToMerge)
                ) {
                    $settingToMerge['value'] = $existingSetting['value'];
                }
            }
        }
        return new ModuleSetting(ModuleSetting::SHOP_MODULE_SETTING, $settingsToMerge);
    }

    /**
     * @param string $value
     * @param array  $shopModuleSettingSettingToMerge
     *
     * @return bool
     */
    private function constraintsAllowThisValue(string $value, array $shopModuleSettingSettingToMerge): bool
    {
        if (isset($shopModuleSettingSettingToMerge['constraints']) && ($shopModuleSettingSettingToMerge['type'] === 'select')) {
            $resultPosition = array_search($value, $shopModuleSettingSettingToMerge['constraints'], true);
            return $resultPosition !== false;
        }
        return true;
    }

    /**
     * @param ModuleConfiguration  $existingModuleConfiguration
     * @param array                $classExtensionsToMerge
     * @param ClassExtensionsChain $chain
     *
     * @return ClassExtensionsChain
     */
    private function compareClassExtensionsAndUpdateChain(
        ModuleConfiguration $existingModuleConfiguration,
        array $classExtensionsToMerge,
        ClassExtensionsChain $chain
    ): ClassExtensionsChain {
        if ($existingModuleConfiguration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $classExtensionsOfExistingModuleConfiguration = $existingModuleConfiguration
                ->getSetting(ModuleSetting::CLASS_EXTENSIONS)
                ->getValue();
            $classExtensionChain = $chain->getChain();
            foreach ($classExtensionsOfExistingModuleConfiguration as $extended => $extension) {
                if (\array_key_exists($extended, $classExtensionsToMerge)) {
                    if ($classExtensionsOfExistingModuleConfiguration[$extended] !== $classExtensionsToMerge[$extended]) {
                        $classExtensionChain[$extended] = $this->replaceExtendedClassAndKeepOrder(
                            $classExtensionsOfExistingModuleConfiguration[$extended],
                            $classExtensionsToMerge[$extended],
                            $classExtensionChain[$extended]
                        );
                        $chain->setChain($classExtensionChain);
                    }
                } else {
                    $chain->removeExtension($extended, $extension);
                }
                unset($classExtensionsToMerge[$extended]);
            }
        }
        $chain->addExtensions($classExtensionsToMerge);

        return $chain;
    }

    /**
     * Converts e.g. the chain [Class1, ClassOld, Class3] to [Class1, ClassNew, Class3]. Keeping the order is important
     * as the order can be changed in OXID eShop admin.
     *
     * @param string $classExtensionsExisting
     * @param string $classExtensionsToMerge
     * @param array  $classExtensionChainData
     *
     * @return array
     */
    private function replaceExtendedClassAndKeepOrder(string $classExtensionsExisting, string $classExtensionsToMerge, array $classExtensionChainData): array
    {
        return str_replace(
            $classExtensionsExisting,
            $classExtensionsToMerge,
            $classExtensionChainData
        );
    }
}
