<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use OxidEsales\EshopCommunity\Internal\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDaoInterface;

/**
 * Class ProductRatingService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Service
 */
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
     *
     * @param RatingDaoInterface               $ratingDao
     * @param ProductRatingDaoInterface        $productRatingDao
     * @param RatingCalculatorServiceInterface $ratingCalculator
     */
    public function __construct(
        RatingDaoInterface                  $ratingDao,
        ProductRatingDaoInterface           $productRatingDao,
        RatingCalculatorServiceInterface    $ratingCalculator
    ) {
        $this->ratingDao = $ratingDao;
        $this->productRatingDao = $productRatingDao;
        $this->ratingCalculator = $ratingCalculator;
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
