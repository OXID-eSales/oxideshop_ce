<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

interface ModuleCacheServiceInterface
{
    /**
     * Invalidate all module related cache items for a given module and a given shop
     *
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidateModuleCache(string $moduleId, int $shopId);
}
