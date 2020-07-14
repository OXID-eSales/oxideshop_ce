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
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

class ModuleInstaller implements ModuleInstallerInterface
{
    /**
     * @var BootstrapModuleInstaller
     */
    private $bootstrapModuleInstaller;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * ModuleInstaller constructor.
     * @param BootstrapModuleInstaller $bootstrapModuleInstaller
     * @param ModuleActivationServiceInterface $moduleActivationService
     * @param ModuleConfigurationDaoInterface $moduleConfigurationDao
     * @param ShopConfigurationDaoInterface $shopConfigurationDao
     * @param ModuleStateServiceInterface $moduleStateService
     */
    public function __construct(
        BootstrapModuleInstaller $bootstrapModuleInstaller,
        ModuleActivationServiceInterface $moduleActivationService,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleStateServiceInterface $moduleStateService
    ) {
        $this->bootstrapModuleInstaller = $bootstrapModuleInstaller;
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleStateService = $moduleStateService;
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
     *
     * @throws ModuleSetupException
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($package->getPackageSourcePath());
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
     *
     * @throws ModuleSetupException
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
