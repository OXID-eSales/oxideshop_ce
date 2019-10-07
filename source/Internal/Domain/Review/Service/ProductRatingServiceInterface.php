<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

interface ProductRatingServiceInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
