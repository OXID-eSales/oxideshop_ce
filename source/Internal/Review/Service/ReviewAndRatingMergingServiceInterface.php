<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ReviewAndRatingMergingServiceInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Service
 */
interface ReviewAndRatingMergingServiceInterface
{
    /**
     * Merges Reviews and Ratings to Collection of ReviewAndRating view objects.
     *
     * @param ArrayCollection $reviews
     * @param ArrayCollection $ratings
     *
     * @return ArrayCollection
     */
    public function mergeReviewAndRating(ArrayCollection $reviews, ArrayCollection $ratings);
}
