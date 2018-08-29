<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

/**
 * Interface ProductRatingServiceInterface
 * @internal
 */
interface ProductRatingServiceInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
