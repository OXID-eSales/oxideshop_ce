<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface ShopConfigurationDaoBridgeInterface
{
    /**
     * @return ShopConfiguration
     */
    public function get(): ShopConfiguration;

    /**
     * @param ShopConfiguration $shopConfiguration
     */
    public function save(ShopConfiguration $shopConfiguration);
}
