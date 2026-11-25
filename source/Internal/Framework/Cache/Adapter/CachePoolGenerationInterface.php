<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

interface CachePoolGenerationInterface
{
    public function getDirectory(int $shopId): string;

    public function renew(int $shopId): void;
}
