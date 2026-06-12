<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use ArrayIterator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class ProductMediaSorting
{
    private ArrayIterator $sorting;

    public function __construct(
        private Id $productId,
        array $orderedIds,
    ) {
        $this->sorting = new ArrayIterator([]);
        foreach ($orderedIds as $id) {
            $this->sorting->append(
                Id::fromString($id)
            );
        }
    }

    public function getProductId(): Id
    {
        return $this->productId;
    }

    public function getSorting(): ArrayIterator
    {
        return $this->sorting;
    }
}
