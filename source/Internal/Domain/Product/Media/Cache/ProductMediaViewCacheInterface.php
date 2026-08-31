<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaViewCacheInterface
{
    public function get(Id $productId, string $viewIdentifier, callable $callback): ProductMediaView;

    /** @return array<string, ProductMediaView> */
    public function getAll(Id $productId, string $viewIdentifier, callable $callback): array;

    public function invalidateForProduct(Id $productId): void;
}
