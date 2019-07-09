<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
interface ModuleConfigurationMergingServiceInterface
{
    /**
     * @param ShopConfiguration   $shopConfiguration
     * @param ModuleConfiguration $moduleConfigurationToMerge
     *
     * @return ShopConfiguration
     */
    public function merge(ShopConfiguration $shopConfiguration, ModuleConfiguration $moduleConfigurationToMerge): ShopConfiguration;
}
