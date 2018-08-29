<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * Class ModuleValidationException
 *
 * This exception should be thrown, if a module validation fails in any point (activation, deactivation, module list, etc)
 */
class ModuleValidationException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type
     *
     * @var string
     */
    protected $type = 'ModuleValidationException';
}
