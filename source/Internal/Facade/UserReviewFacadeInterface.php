<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

/**
 * Interface UserReviewFacadeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
interface UserReviewFacadeInterface
{
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
