<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

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

    public function delete(Rating $rating);
}
