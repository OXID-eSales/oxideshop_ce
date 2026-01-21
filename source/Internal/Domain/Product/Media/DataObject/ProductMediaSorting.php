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
        array $sortedIds
    ) {
        $this->sorting = new ArrayIterator([]);
        foreach ($sortedIds as $id) {
            $this->sorting->append(
                Id::fromString($id)
            );
        }
    }

    public function getSorting(): ArrayIterator
    {
        return $this->sorting;
    }

    public function __toString(): string
    {
        $ids = '';
        foreach ($this->sorting as $id) {
            $ids .= "'$id',";
        }
        return rtrim(
            $ids,
            ','
        );
    }
}
