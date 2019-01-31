<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
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
        if ($shopConfiguration->hasModuleConfiguration($moduleConfigurationToMerge)) {

        } else {
            $shopConfiguration->addModuleConfiguration($moduleConfigurationToMerge);
            $shopConfiguration = $this->addClassExtensionsToChain($moduleConfigurationToMerge, $shopConfiguration);
        }

        return $shopConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param ShopConfiguration   $shopConfiguration
     *
     * @return ShopConfiguration
     */
    private function addClassExtensionsToChain(
        ModuleConfiguration $moduleConfiguration,
        ShopConfiguration $shopConfiguration
    ): ShopConfiguration {
        if ($moduleConfiguration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $classExtensions = $moduleConfiguration->getSetting(ModuleSetting::CLASS_EXTENSIONS);

            $classExtensionChain = $shopConfiguration->getChain(Chain::CLASS_EXTENSIONS);
            $classExtensionChain->addExtensions($classExtensions->getValue());
        }

        return $shopConfiguration;
    }
}
