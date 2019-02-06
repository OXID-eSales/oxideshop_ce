<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;

/**
 * @internal
 */
interface ModuleInstallerInterface
{
    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package);

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool;
}
