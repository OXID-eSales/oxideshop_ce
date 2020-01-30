<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

interface ModuleConfigurationInstallerInterface
{
    /**
     * @param string $moduleSourcePath
     * @param string $moduleTargetPath
     */
    public function install(string $moduleSourcePath, string $moduleTargetPath): void;

    /**
     * @param string $modulePath
     */
    public function uninstall(string $modulePath): void;

    /**
     * @param string $packagePath
     * @return bool
     */
    public function isInstalled(string $packagePath): bool;
}
