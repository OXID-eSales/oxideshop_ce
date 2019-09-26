<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Smarty\Configuration;

/**
 * Interface SmartyResourcesDataProviderInterface
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Configuration
 */
interface SmartyResourcesDataProviderInterface
{
    /**
     * Returns an array of resources.
     *
     * @return array
     */
    public function getResources(): array;
}
