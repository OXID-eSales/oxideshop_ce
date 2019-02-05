<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

/**
 * @internal
 */
class ModuleInstaller implements ModuleInstallerInterface
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
     * @param string $packagePath
     */
    public function install(string $packagePath)
    {
        $this->moduleFilesInstaller->forceCopy($packagePath);
        $this->moduleConfigurationInstaller->install($packagePath);
    }

    /**
     * @param string $packagePath
     * @return bool
     */
    public function isInstalled(string $packagePath): bool
    {
        return $this->moduleFilesInstaller->isInstalled($packagePath)
            && $this->moduleConfigurationInstaller->isInstalled($packagePath);
    }
}
