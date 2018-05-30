<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

use OxidEsales\EshopCommunity\Internal\Review\Service\ProductRatingServiceInterface;

/**
 * @internal
 */
class ProductRatingBridge implements ProductRatingBridgeInterface
{
    /**
     * @var ProductRatingServiceInterface
     */
    private $productRatingService;

    /**
     * ProductRatingBridge constructor.
     * @param ProductRatingServiceInterface $productRatingService
     */
    public function __construct(ProductRatingServiceInterface $productRatingService)
    {
        $this->productRatingService = $productRatingService;
    }

    /**
     * @param string $productId
     */
    public function updateProductRating($productId)
    {
        $this->productRatingService->updateProductRating($productId);
    }
}
