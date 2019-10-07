<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;

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
