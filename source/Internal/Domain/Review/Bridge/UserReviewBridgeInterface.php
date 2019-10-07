<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Annotation\StableAnnotation;

/**
 * @stable
 * @see StableAnnotation
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
