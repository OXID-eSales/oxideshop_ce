<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge;

/**
 * @internal
 */
interface ClassExtensionChainBridgeInterface
{
    /**
     * @param int $shopId
     */
    public function updateChain(int $shopId);
}
