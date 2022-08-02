<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge;

/**
 * @stable
 * @deprecated will be completely removed.
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface ClassExtensionChainBridgeInterface
{
    /**
     * @param int $shopId
     */
    public function updateChain(int $shopId);
}
