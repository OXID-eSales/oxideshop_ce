<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

class FilesystemModuleCacheBridge implements ModuleCacheServiceBridgeInterface
{
    public function __construct(
        private ModuleCacheServiceInterface $moduleCacheService
    ) {
    }

    public function invalidateAll(): void
    {
        $this->moduleCacheService->invalidateAll();
    }
}
