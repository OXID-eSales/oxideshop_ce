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
     * Invalidate all module related cache items for a given module and a given shop.
     */
    public function invalidate(string $moduleId, int $shopId): void;

    public function put(string $key, int $shopId, array $data): void;

    public function get(string $key, int $shopId): array;

    public function exists(string $key, int $shopId): bool;
}
