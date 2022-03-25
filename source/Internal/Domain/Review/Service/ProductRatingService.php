<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDaoInterface;

class ProductRatingService implements ProductRatingServiceInterface
{
    /**
     * ProductRatingService constructor.
     */
    public function __construct(private RatingDaoInterface $ratingDao, private ProductRatingDaoInterface $productRatingDao, private RatingCalculatorServiceInterface $ratingCalculator)
    {
    }

    /**
     * @param string $productId
     */
    public function updateProductRating($productId)
    {
        $ratings = $this
            ->ratingDao
            ->getRatingsByProductId($productId);

        $ratingAverage = $this
            ->ratingCalculator
            ->getAverage($ratings);

        $ratingCount = $ratings->count();

        $productRating = $this->productRatingDao->getProductRatingById($productId);
        $productRating
            ->setRatingAverage($ratingAverage)
            ->setRatingCount($ratingCount);

        $this->productRatingDao->update($productRating);
    }
}
