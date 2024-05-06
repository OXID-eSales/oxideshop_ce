<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache;

interface ShopCacheCleanerInterface
{
    public function clear(int $shopId): void;

    public function clearAll(): void;
}
