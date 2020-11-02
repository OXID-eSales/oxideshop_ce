<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface ShopConfigurationDaoBridgeInterface
{
    public function get(): ShopConfiguration;

    public function save(ShopConfiguration $shopConfiguration);
}
