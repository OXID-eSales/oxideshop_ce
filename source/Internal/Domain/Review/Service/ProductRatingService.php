<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\RatingDaoInterface;

class ProductRatingService implements ProductRatingServiceInterface
{
    /**
     * @var RatingDaoInterface
     */
    private $ratingDao;

    /**
     * @var ProductRatingDaoInterface
     */
    private $productRatingDao;

    /**
     * @var RatingCalculatorServiceInterface
     */
    private $ratingCalculator;

    /**
     * ProductRatingService constructor.
     */
    public function __construct(
        RatingDaoInterface $ratingDao,
        ProductRatingDaoInterface $productRatingDao,
        RatingCalculatorServiceInterface $ratingCalculator
    ) {
        $this->ratingDao = $ratingDao;
        $this->productRatingDao = $productRatingDao;
        $this->ratingCalculator = $ratingCalculator;
    }

    /**
     * @param string $productId
     */
    public function updateProductRating($productId): void
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
