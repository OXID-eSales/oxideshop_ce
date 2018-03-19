<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Dao;

use OxidEsales\EshopCommunity\Internal\DataObject\ProductRating;

/**
 * Interface ProductDaoInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Dao
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
