<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Dao;

use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;

/**
 * @internal
 */
interface OxidEshopPackageDaoInterface
{

    /**
     * @param string $packagePath
     *
     * @return OxidEshopPackage
     */
    public function getPackage(string $packagePath) : OxidEshopPackage;
}
