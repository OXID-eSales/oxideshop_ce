<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ClearShopCacheEvent extends Event
{
    public function __construct(
        private readonly int $shopId
    ) {
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
