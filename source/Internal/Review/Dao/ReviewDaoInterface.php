<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;

/**
 * Interface ReviewDaoInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Dao
 */
interface ReviewDaoInterface
{
    /**
     * Returns User Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewsByUserId($userId);

    /**
     * @param Review $review
     */
    public function delete(Review $review);
}
