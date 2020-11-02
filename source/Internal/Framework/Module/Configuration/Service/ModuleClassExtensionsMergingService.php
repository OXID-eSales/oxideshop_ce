<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

class ModuleClassExtensionsMergingService implements ModuleClassExtensionsMergingServiceInterface
{
    /**
     * @throws ExtensionNotInChainException
     * @throws ModuleConfigurationNotFoundException
     */
    public function merge(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfiguration
    ): ClassExtensionsChain {
        $chain = $shopConfiguration->getClassExtensionsChain();

        if (!$shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
            $chain->addExtensions($moduleConfiguration->getClassExtensions());
        } else {
            $chain = $this->addNewModuleExtensionsToChain($moduleConfiguration, $shopConfiguration, $chain);
            $chain = $this->replaceExistingModuleExtensionsInChain($moduleConfiguration, $shopConfiguration, $chain);
            $chain = $this->removeDeletedModuleExtensionsFromChain($moduleConfiguration, $shopConfiguration, $chain);
        }

        return $chain;
    }

    /**
     * @throws ModuleConfigurationNotFoundException
     * @throws ExtensionNotInChainException
     */
    private function removeDeletedModuleExtensionsFromChain(
        ModuleConfiguration $moduleConfiguration,
        ShopConfiguration $shopConfiguration,
        ClassExtensionsChain $classExtensionChain
    ): ClassExtensionsChain {
        $existentModuleConfiguration = $shopConfiguration->getModuleConfiguration(
            $moduleConfiguration->getId()
        );

        foreach ($existentModuleConfiguration->getClassExtensions() as $extension) {
            if (!$this->isExtendingShopClass($extension, $moduleConfiguration->getClassExtensions())) {
                $classExtensionChain->removeExtension($extension);
            }
        }

        return $classExtensionChain;
    }

    /**
     * @throws ModuleConfigurationNotFoundException
     */
    private function replaceExistingModuleExtensionsInChain(
        ModuleConfiguration $moduleConfiguration,
        ShopConfiguration $shopConfiguration,
        ClassExtensionsChain $chain
    ): ClassExtensionsChain {
        $existentModuleConfiguration = $shopConfiguration->getModuleConfiguration(
            $moduleConfiguration->getId()
        );

        foreach ($existentModuleConfiguration->getClassExtensions() as $existingExtension) {
            foreach ($moduleConfiguration->getClassExtensions() as $newExtension) {
                if ($this->areExtensionsEqual($existingExtension, $newExtension)) {
                    $this->replaceExistingExtension($chain, $existingExtension, $newExtension);
                }
            }
        }

        return $chain;
    }

    /**
     * @throws ModuleConfigurationNotFoundException
     */
    private function addNewModuleExtensionsToChain(
        ModuleConfiguration $moduleConfiguration,
        ShopConfiguration $shopConfiguration,
        ClassExtensionsChain $chain
    ): ClassExtensionsChain {
        foreach ($moduleConfiguration->getClassExtensions() as $classExtension) {
            $existentModuleConfiguration = $shopConfiguration->getModuleConfiguration(
                $moduleConfiguration->getId()
            );

            if (!$existentModuleConfiguration->isExtendingShopClass($classExtension->getShopClassName())) {
                $chain->addExtension($classExtension);
            }
        }

        return $chain;
    }

    /**
     * @param ClassExtension[] $newClassExtensions
     */
    private function isExtendingShopClass(ClassExtension $existingClassExtension, array $newClassExtensions): bool
    {
        foreach ($newClassExtensions as $newExtension) {
            if ($newExtension->getShopClassName() === $existingClassExtension->getShopClassName()) {
                return true;
            }
        }

        return false;
    }

    private function areExtensionsEqual(ClassExtension $existingExtension, ClassExtension $newExtension): bool
    {
        return $existingExtension->getShopClassName() === $newExtension->getShopClassName()
               && $existingExtension->getModuleExtensionClassName() !==
                  $newExtension->getModuleExtensionClassName();
    }

    /**
     * Converts e.g. the chain [Class1, ClassOld, Class3] to [Class1, ClassNew, Class3]. Keeping the order is important
     * as the order can be changed in OXID eShop admin.
     */
    private function replaceExistingExtension(
        ClassExtensionsChain $chain,
        ClassExtension $existingExtension,
        ClassExtension $newExtension
    ): void {
        $classExtensionChain = $chain->getChain();
        $shopClassNamespaceInChain = $classExtensionChain[$existingExtension->getShopClassName()];
        foreach ($shopClassNamespaceInChain as $key => $existingExtensionInChain) {
            if ($existingExtensionInChain === $existingExtension->getModuleExtensionClassName()) {
                $classExtensionChain[$existingExtension->getShopClassName()][$key] =
                    $newExtension->getModuleExtensionClassName();
            }
        }

        $chain->setChain($classExtensionChain);
    }
}
