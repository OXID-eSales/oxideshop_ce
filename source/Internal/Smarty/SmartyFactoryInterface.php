<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Creates and configures the Smarty object.
 */
interface SmartyFactoryInterface
{
    /**
     * @return \Smarty
     */
    public function getSmarty();
}
