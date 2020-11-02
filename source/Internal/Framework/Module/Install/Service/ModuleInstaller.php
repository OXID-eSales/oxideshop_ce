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
     * @var ModuleInstallerInterface
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
     */
    public function __construct(
        ModuleInstallerInterface $bootstrapModuleInstaller,
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

    public function install(OxidEshopPackage $package): void
    {
        $this->bootstrapModuleInstaller->install($package);
    }

    /**
     * @throws ModuleSetupException
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($package->getPackageSourcePath());
        $this->deactivateModule($moduleConfiguration->getId());

        $this->bootstrapModuleInstaller->uninstall($package);
    }

    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->bootstrapModuleInstaller->isInstalled($package);
    }

    /**
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
