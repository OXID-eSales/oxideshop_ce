<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

/**
 * Interface ProductRatingFacadeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
interface ProductRatingFacadeInterface
{
    /**
     * @param string $productId
     */
    public function updateProductRating($productId);
}
