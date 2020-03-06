<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject;

/**
 * Interface ParameterInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject
 */
interface ParameterInterface
{
    /**
     * Returns the parameters for the function
     *
     * @return mixed
     */
    public function getParameter();
}
