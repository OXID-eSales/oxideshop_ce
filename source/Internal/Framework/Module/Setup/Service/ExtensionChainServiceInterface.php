<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

/**
 * @deprecated will be completely removed.
 */
interface ExtensionChainServiceInterface
{
    /**
     * @param int $shopId
     */
    public function updateChain(int $shopId);
}
