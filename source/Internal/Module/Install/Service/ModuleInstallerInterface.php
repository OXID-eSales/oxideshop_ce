<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

/**
 * @internal
 */
interface ModuleInstallerInterface
{
    /**
     * @param string $packagePath
     */
    public function install(string $packagePath);

    /**
     * @param string $packagePath
     * @return bool
     */
    public function isInstalled(string $packagePath): bool;
}
