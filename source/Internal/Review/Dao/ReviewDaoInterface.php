<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Dao;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @param string $userId
     * @param string $reviewId
     *
     * @return bool
     */
    public function deleteReview($userId, $reviewId);
}
