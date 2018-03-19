<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface RatingDaoInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Dao
 */
interface RatingDaoInterface
{
    /**
     * Returns User Ratings.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatingsByUserId($userId);

    /**
     * Returns Ratings for a product.
     *
     * @param string $productId
     *
     * @return ArrayCollection
     */
    public function getRatingsByProductId($productId);
}
