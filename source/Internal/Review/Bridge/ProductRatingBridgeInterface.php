<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

/**
 * @internal
 */
interface ProductRatingBridgeInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
