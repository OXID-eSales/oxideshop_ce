<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ProductRatingServiceInterface;

class ProductRatingBridge implements ProductRatingBridgeInterface
{
    /**
     * ProductRatingBridge constructor.
     */
    public function __construct(private ProductRatingServiceInterface $productRatingService)
    {
    }

    /**
     * @param string $productId
     */
    public function updateProductRating($productId)
    {
        $this->productRatingService->updateProductRating($productId);
    }
}
