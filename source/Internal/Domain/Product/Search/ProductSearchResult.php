<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Search;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class ProductSearchResult
{
    /** @var list<Id> */
    private array $productIds;

    /** @param list<Id> $productIds */
    public function __construct(
        array $productIds,
        private int $total,
    ) {
        $this->productIds = array_values($productIds);
    }

    /** @return list<Id> */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
