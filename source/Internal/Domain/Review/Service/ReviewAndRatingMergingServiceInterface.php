<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;

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
