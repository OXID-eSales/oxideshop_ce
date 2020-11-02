<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

class ProductRatingDataMapper implements ProductRatingDataMapperInterface
{
    public function map(ProductRating $productRating, array $data): ProductRating
    {
        $productRating
            ->setProductId($data['OXID'])
            ->setRatingAverage($data['OXRATING'])
            ->setRatingCount($data['OXRATINGCNT']);

        return $productRating;
    }

    public function getData(ProductRating $productRating): array
    {
        return [
            'OXID' => $productRating->getProductId(),
            'OXRATING' => $productRating->getRatingAverage(),
            'OXRATINGCNT' => $productRating->getRatingCount(),
        ];
    }

    public function getPrimaryKey(ProductRating $productRating): array
    {
        return [
            'OXID' => $productRating->getProductId(),
        ];
    }
}
