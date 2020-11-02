<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

interface ProductRatingDataMapperInterface
{
    public function map(ProductRating $productRating, array $data): ProductRating;

    public function getData(ProductRating $productRating): array;

    public function getPrimaryKey(ProductRating $productRating): array;
}
