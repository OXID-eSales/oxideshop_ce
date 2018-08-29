<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Review\DataObject\ProductRating;

/**
 * Interface ProductDaoInterface
 * @internal
 */
interface ProductRatingDaoInterface
{
    /**
     * @param ProductRating $productRating
     */
    public function update(ProductRating $productRating);

    /**
     * @param string $productId
     *
     * @return ProductRating
     */
    public function getProductRatingById($productId);
}
