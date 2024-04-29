<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

/**
 * @deprecated v7.2 and will be removed as of v8.0
 */
interface ModuleCacheServiceBridgeInterface
{
    public function invalidateAll(): void;
}
