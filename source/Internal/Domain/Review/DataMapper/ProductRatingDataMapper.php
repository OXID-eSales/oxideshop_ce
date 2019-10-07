<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

class ProductRatingDataMapper implements ProductRatingDataMapperInterface
{
    /**
     * @param ProductRating $productRating
     * @param array         $data
     *
     * @return ProductRating
     */
    public function map(ProductRating $productRating, array $data): ProductRating
    {
        $productRating
            ->setProductId($data['OXID'])
            ->setRatingAverage($data['OXRATING'])
            ->setRatingCount($data['OXRATINGCNT']);

        return $productRating;
    }

    /**
     * @param ProductRating $productRating
     *
     * @return array
     */
    public function getData(ProductRating $productRating): array
    {
        return [
            'OXID'        => $productRating->getProductId(),
            'OXRATING'    => $productRating->getRatingAverage(),
            'OXRATINGCNT' => $productRating->getRatingCount(),
        ];
    }

    /**
     * @param ProductRating $productRating
     *
     * @return array
     */
    public function getPrimaryKey(ProductRating $productRating): array
    {
        return [
            'OXID' => $productRating->getProductId(),
        ];
    }
}
