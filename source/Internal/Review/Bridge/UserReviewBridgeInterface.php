<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

/**
 * Interface UserReviewBridgeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Bridge
 */
interface UserReviewBridgeInterface
{
    /**
     * Delete a Review.
     *
     * @param string $userId
     * @param string $reviewId
     */
    public function deleteReview($userId, $reviewId);
}
