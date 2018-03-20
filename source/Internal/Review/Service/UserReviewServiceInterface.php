<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserReviewServiceInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Service
 */
interface UserReviewServiceInterface
{
    /**
     * Returns User Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviews($userId);

    /**
     * Delete a Review.
     *
     * @param string $userId
     * @param string $reviewId
     *
     * @return bool
     */
    public function deleteReview($userId, $reviewId);
}
