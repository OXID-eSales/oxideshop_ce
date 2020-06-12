<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

class ModuleConfigurationMergingService implements ModuleConfigurationMergingServiceInterface
{
    /**
     * @var SettingsMergingService
     */
    private $settingsMergingService;

    /**
     * @var ModuleClassExtensionsMergingService
     */
    private $classExtensionsMergingService;

    /**
     * @param SettingsMergingServiceInterface $moduleSettingsMergingService
     * @param ModuleClassExtensionsMergingServiceInterface $classExtensionsMergingService
     */
    public function __construct(
        SettingsMergingServiceInterface $moduleSettingsMergingService,
        ModuleClassExtensionsMergingServiceInterface $classExtensionsMergingService
    ) {
        $this->settingsMergingService = $moduleSettingsMergingService;
        $this->classExtensionsMergingService = $classExtensionsMergingService;
    }

    /**
     * @inheritDoc
     * @throws ModuleConfigurationNotFoundException
     * @throws ExtensionNotInChainException
     */
    public function merge(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfiguration
    ): ShopConfiguration {
        $moduleConfigurationClone = $this->cloneModuleConfiguration($moduleConfiguration);

        $mergedClassExtensionChain = $this->classExtensionsMergingService->merge(
            $shopConfiguration,
            $moduleConfigurationClone
        );
        $shopConfiguration->setClassExtensionsChain($mergedClassExtensionChain);

        $mergedModuleConfiguration = $this->settingsMergingService->merge(
            $shopConfiguration,
            $moduleConfigurationClone
        );

        $this->setConfiguredOptionToMergedConfiguration($shopConfiguration, $mergedModuleConfiguration);

        $shopConfiguration->addModuleConfiguration($mergedModuleConfiguration);

        return $shopConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @return ModuleConfiguration
     */
    private function cloneModuleConfiguration(ModuleConfiguration $moduleConfiguration): ModuleConfiguration
    {
        $moduleSettingClones = [];
        foreach ($moduleConfiguration->getModuleSettings() as $moduleSetting) {
            $moduleSettingClones[] = clone $moduleSetting;
        }
        $moduleConfigurationClone = clone $moduleConfiguration;
        $moduleConfigurationClone->setModuleSettings($moduleSettingClones);
        return $moduleConfigurationClone;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param ModuleConfiguration $mergedModuleConfiguration
     * @throws ModuleConfigurationNotFoundException
     */
    private function setConfiguredOptionToMergedConfiguration(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $mergedModuleConfiguration
    ): void {
        if ($shopConfiguration->hasModuleConfiguration($mergedModuleConfiguration->getId())) {
            $isConfigured = $shopConfiguration->getModuleConfiguration($mergedModuleConfiguration->getId())->isConfigured();
            $mergedModuleConfiguration->setConfigured($isConfigured);
        }
    }
}
