<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Pool;

use Psr\Cache\CacheItemPoolInterface;

interface CacheItemPoolFactoryInterface
{
    public function create(int $shopId): CacheItemPoolInterface;
}
