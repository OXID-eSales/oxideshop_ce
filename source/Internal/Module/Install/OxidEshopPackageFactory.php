<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;


use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\OxidEshopPackage;

/**
 * Class OxidEshopPackageFactory
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\Setup\Install
 */
class OxidEshopPackageFactory implements OxidEshopPackageFactoryInterface
{

    /**
     * @param string $packagePath
     *
     * @return OxidEshopPackage
     */
    public function getPackage(string $packagePath): OxidEshopPackage
    {
        $package = new OxidEshopPackage();
        return $package;
    }

}
