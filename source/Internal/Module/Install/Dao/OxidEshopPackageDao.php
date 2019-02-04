<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Dao;

use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;

/**
 * Class OxidEshopPackageDao
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\Setup\Install
 */
class OxidEshopPackageDao implements OxidEshopPackageDaoInterface
{
    /**
     * @param string $packagePath
     *
     * @return OxidEshopPackage
     */
    public function getPackage(string $packagePath): OxidEshopPackage
    {
        $package = new OxidEshopPackage('dummy', []);
        return $package;
    }
}
