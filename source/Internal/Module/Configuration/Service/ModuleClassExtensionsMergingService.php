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
class ModuleClassExtensionsMergingService implements ModuleClassExtensionsMergingServiceInterface
{
    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfiguration
     *
     * @return ClassExtensionsChain
     */
    public function merge(
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
