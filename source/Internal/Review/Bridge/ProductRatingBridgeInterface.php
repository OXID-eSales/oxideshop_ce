<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

/**
 * Interface ProductRatingBridgeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Bridge
 */
interface ProductRatingBridgeInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
