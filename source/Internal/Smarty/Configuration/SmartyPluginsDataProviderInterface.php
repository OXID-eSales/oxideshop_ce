<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty\Configuration;

/**
 * Interface SmartyPluginsDataProviderInterface
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Configuration
 */
interface SmartyPluginsDataProviderInterface
{
    /**
     * @return array
     */
    public function getPlugins(): array;
}
