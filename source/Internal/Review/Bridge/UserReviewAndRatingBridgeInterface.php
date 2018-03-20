<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

/**
 * Interface UserReviewAndRatingBridgeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Bridge
 */
interface UserReviewAndRatingBridgeInterface
{
    /**
     * Get number of reviews by given user.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getReviewAndRatingListCount($userId);

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     *
     * @return array
     */
    public function getReviewAndRatingList($userId);
}
