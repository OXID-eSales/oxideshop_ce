<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Interface oxIModuleValidator
 * @deprecated since v6.4.0 (2019-05-24); Validation was moved to Internal\Module package and will be executed during the module activation.
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
