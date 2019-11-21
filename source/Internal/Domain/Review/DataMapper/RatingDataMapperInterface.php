<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

interface RatingDataMapperInterface
{
    /**
     * @param Rating $rating
     * @param array  $data
     *
     * @return Rating
     */
    public function map(Rating $rating, array $data): Rating;

    /**
     * @param Rating $rating
     *
     * @return array
     */
    public function getData(Rating $rating): array;

    /**
     * @param Rating $rating
     *
     * @return array
     */
    public function getPrimaryKey(Rating $rating): array;
}
