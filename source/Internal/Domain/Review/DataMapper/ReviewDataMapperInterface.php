<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Review;

interface ReviewDataMapperInterface
{
    public function map(Review $review, array $data): Review;

    public function getData(Review $review): array;

    public function getPrimaryKey(Review $review): array;
}
