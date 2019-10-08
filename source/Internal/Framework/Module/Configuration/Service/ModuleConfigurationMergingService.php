<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;

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
     * @param SettingsMergingServiceInterface    $moduleSettingsMergingService
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
     * @param ShopConfiguration $shopConfiguration
     * @param ModuleConfiguration $moduleConfigurationToMerge
     *
     * @throws ModuleConfigurationNotFoundException
     * @throws ExtensionNotInChainException
     *
     * @return ShopConfiguration
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException
     * @throws \OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException
     */
    public function merge(
        ShopConfiguration $shopConfiguration,
        ModuleConfiguration $moduleConfigurationToMerge
    ): ShopConfiguration {
        $mergedClassExtensionChain = $this->classExtensionsMergingService->merge(
            $shopConfiguration,
            $moduleConfigurationToMerge
        );
        $shopConfiguration->setClassExtensionsChain($mergedClassExtensionChain);

        $mergedModuleConfiguration = $this->settingsMergingService->merge(
            $shopConfiguration,
            $moduleConfigurationToMerge
        );
        $shopConfiguration->addModuleConfiguration($mergedModuleConfiguration);

        return $shopConfiguration;
    }
}
