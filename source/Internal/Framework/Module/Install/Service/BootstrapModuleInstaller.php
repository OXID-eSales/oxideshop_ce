<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;

class BootstrapModuleInstaller implements ModuleInstallerInterface
{
    /**
     * @var ModuleFilesInstallerInterface
     */
    private $moduleFilesInstaller;

    /**
     * @var ModuleConfigurationInstallerInterface
     */
    private $moduleConfigurationInstaller;

    /**
     * ModuleInstaller constructor.
     *
     * @param ModuleFilesInstallerInterface         $moduleFilesInstaller
     * @param ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
     */
    public function __construct(
        ModuleFilesInstallerInterface $moduleFilesInstaller,
        ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
    ) {
        $this->moduleFilesInstaller = $moduleFilesInstaller;
        $this->moduleConfigurationInstaller = $moduleConfigurationInstaller;
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $this->moduleFilesInstaller->install($package);
        $this->moduleConfigurationInstaller->install($package->getPackageSourcePath(), $package->getTargetDirectory());
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $this->moduleConfigurationInstaller->uninstall($package->getPackageSourcePath());
        $this->moduleFilesInstaller->uninstall($package);
    }

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->moduleFilesInstaller->isInstalled($package)
               && $this->moduleConfigurationInstaller->isInstalled($package->getPackageSourcePath());
    }
}
