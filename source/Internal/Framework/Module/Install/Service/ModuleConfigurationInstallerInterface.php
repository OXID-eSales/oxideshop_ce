<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

interface ModuleConfigurationInstallerInterface
{
    /**
     * @param string $moduleSourcePath
     */
    public function install(string $moduleSourcePath): void;

    /**
     * @param string $moduleSourcePath
     */
    public function uninstall(string $moduleSourcePath): void;

    /**
     * @param string $moduleId
     */
    public function uninstallById(string $moduleId): void;

    /**
     * @param string $moduleSourcePath
     *
     * @return bool
     */
    public function isInstalled(string $moduleSourcePath): bool;
}
