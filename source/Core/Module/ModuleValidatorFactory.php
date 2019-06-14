<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

/**
 * Module validators factory class.
 *
 * @deprecated since v6.4.0 (2019-03-22); Module metadata validation moved to Internal\Module package.
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleValidatorFactory
{
    /**
     * Return module validator by provided type.
     * Returned validator implements interface oxIModuleValidator.
     *
     * @return oxModuleMetadataValidator
     */
    public function getModuleMetadataValidator()
    {
        return oxNew(\OxidEsales\Eshop\Core\Module\ModuleMetadataValidator::class);
    }
}
