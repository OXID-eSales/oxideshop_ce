<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Review;

interface ReviewDataMapperInterface
{
    /**
     * @param Review $review
     * @param array  $data
     *
     * @return Review
     */
    public function map(Review $review, array $data): Review;

    /**
     * @param Review $review
     *
     * @return array
     */
    public function getData(Review $review): array;

    /**
     * @param Review $review
     *
     * @return array
     */
    public function getPrimaryKey(Review $review): array;
}
