<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Interface oxIModuleValidator
 */
interface IModuleValidator
{

    /**
     * Validates module information.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $oModule object to validate metadata.
     *
     * @return bool
     */
    public function validate(\OxidEsales\Eshop\Core\Module\Module $oModule);
}
