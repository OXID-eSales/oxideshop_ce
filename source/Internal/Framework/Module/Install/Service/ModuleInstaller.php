<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

class ModuleInstaller implements ModuleInstallerInterface
{
    public function __construct(
        private ModuleInstallerInterface $bootstrapModuleInstaller,
        private ModuleActivationServiceInterface $moduleActivationService,
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ModuleStateServiceInterface $moduleStateService
    ) {
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $this->bootstrapModuleInstaller->install($package);
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($package->getPackagePath());
        $this->deactivateModule($moduleConfiguration->getId());

        $this->bootstrapModuleInstaller->uninstall($package);
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->bootstrapModuleInstaller->isInstalled($package);
    }

    /**
     * @param string $moduleId
     */
    private function deactivateModule(string $moduleId): void
    {
        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if (
                $shopConfiguration->hasModuleConfiguration($moduleId)
                && $this->moduleStateService->isActive($moduleId, $shopId)
            ) {
                $this->moduleActivationService->deactivate($moduleId, $shopId);
            }
        }
    }
}
