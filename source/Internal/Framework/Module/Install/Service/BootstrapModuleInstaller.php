<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use Symfony\Component\Filesystem\Path;

class BootstrapModuleInstaller implements ModuleInstallerInterface
{
    private const BOOTSTRAP_SERVICES_FILE_NAME = 'bootstrap-services.yaml';

    public function __construct(
        private ModuleFilesInstallerInterface $moduleFilesInstaller,
        private ModuleConfigurationInstallerInterface $moduleConfigurationInstaller,
        private ProjectYamlImportServiceInterface $projectYamlImportService
    ) {
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $this->moduleFilesInstaller->install($package);
        $this->moduleConfigurationInstaller->install($package->getPackagePath());
        $this->addBootstrapServices($package->getPackagePath());
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $this->removeBootstrapServices($package->getPackagePath());
        $this->moduleConfigurationInstaller->uninstall($package->getPackagePath());
        $this->moduleFilesInstaller->uninstall($package);
    }

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->moduleFilesInstaller->isInstalled($package)
               && $this->moduleConfigurationInstaller->isInstalled($package->getPackagePath());
    }

    private function addBootstrapServices(string $modulePath): void
    {
        $bootstrapServicesFilePath = Path::join($modulePath, self::BOOTSTRAP_SERVICES_FILE_NAME);
        if (is_file($bootstrapServicesFilePath)) {
            $this->projectYamlImportService->addImportFromFilePath($bootstrapServicesFilePath);
        }
    }

    private function removeBootstrapServices(string $modulePath): void
    {
        $bootstrapServicesFilePath = Path::join($modulePath, self::BOOTSTRAP_SERVICES_FILE_NAME);
        if (is_file($bootstrapServicesFilePath)) {
            $this->projectYamlImportService->removeImportFromFilePath($bootstrapServicesFilePath);
        }
    }
}
