<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Facade;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserReviewAndRatingFacadeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
interface UserReviewAndRatingFacadeInterface
{
    /**
     * @param string $reviewId
     * @param string $userId
     */
    public function deleteReview($reviewId, $userId);

    /**
     * @param string $ratingId
     * @param string $userId
     */
    public function deleteRating($ratingId, $userId);

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     * @param int    $itemsPerPage
     * @param int    $offset
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId, $itemsPerPage, $offset);
}
