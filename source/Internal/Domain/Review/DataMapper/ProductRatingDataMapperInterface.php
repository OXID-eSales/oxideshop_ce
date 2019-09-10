<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

interface ProductRatingDataMapperInterface
{
    /**
     * @param ProductRating $productRating
     * @param array         $data
     *
     * @return ProductRating
     */
    public function map(ProductRating $productRating, array $data): ProductRating;

    /**
     * @param ProductRating $productRating
     *
     * @return array
     */
    public function getData(ProductRating $productRating): array;

    /**
     * @param ProductRating $productRating
     *
     * @return array
     */
    public function getPrimaryKey(ProductRating $productRating): array;
}
