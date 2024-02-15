<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

class ModuleCacheBridge implements ModuleCacheServiceBridgeInterface
{
    public function __construct(
        private readonly ModuleCacheServiceInterface $moduleCacheService
    ) {
    }

    public function invalidateAll(): void
    {
        $this->moduleCacheService->invalidateAll();
    }
}
