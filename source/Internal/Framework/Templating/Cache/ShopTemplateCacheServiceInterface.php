<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

interface ShopTemplateCacheServiceInterface
{
    public function getCacheDirectory(int $shopId): string;

    public function invalidateCache(int $shopId): void;

    public function invalidateAllShopsCache(): void;
}
