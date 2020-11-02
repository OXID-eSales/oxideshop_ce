<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;

interface ModuleInstallerInterface
{
    public function install(OxidEshopPackage $package);

    public function uninstall(OxidEshopPackage $package): void;

    public function isInstalled(OxidEshopPackage $package): bool;
}
