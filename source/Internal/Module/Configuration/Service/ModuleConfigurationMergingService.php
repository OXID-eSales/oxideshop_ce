<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ModuleConfigurationMergingService implements ModuleConfigurationMergingServiceInterface
{
    /**
     * @var ShopModuleSettingsMergingService
     */
    private $settingsMergingService;

    /**
     * @var ModuleClassExtensionsMergingService
     */
    private $classExtensionsMergingService;

    /**
     * @param ShopModuleSettingsMergingServiceInterface    $moduleSettingsMergingService
     * @param ModuleClassExtensionsMergingServiceInterface $classExtensionsMergingService
     */
    public function __construct(
        ShopModuleSettingsMergingServiceInterface $moduleSettingsMergingService,
        ModuleClassExtensionsMergingServiceInterface $classExtensionsMergingService
    ) {
        $this->settingsMergingService = $moduleSettingsMergingService;
        $this->classExtensionsMergingService = $classExtensionsMergingService;
    }

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
        $mergedClassExtensionChain = $this->classExtensionsMergingService->merge($shopConfiguration, $moduleConfigurationToMerge);
        $shopConfiguration->setClassExtensionsChain($mergedClassExtensionChain);

        $mergedModuleConfiguration = $this->settingsMergingService->merge($shopConfiguration, $moduleConfigurationToMerge);
        $shopConfiguration->addModuleConfiguration($mergedModuleConfiguration);

        return $shopConfiguration;
    }
}
