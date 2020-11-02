<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface ProductRatingBridgeInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
