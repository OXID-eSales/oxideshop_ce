<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject\ParameterInterface;

/**
 * Interface EventModuleParametersInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator
 */
interface EventModuleParametersInterface
{
    /**
     * Returns the parameters for the function
     * @return array
     */
    public function getParameters();

    /**
     * @param ParameterInterface $parameter
     */
    public function addParameter(ParameterInterface $parameter);
}
