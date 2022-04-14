<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\ViewDataObject\ReviewAndRating;

class UserReviewAndRatingService implements UserReviewAndRatingServiceInterface
{
    public function __construct(
        private UserReviewServiceInterface $userReviewService,
        private UserRatingServiceInterface $userRatingService,
        private ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
    ) {
    }

    /**
     * Get number of reviews by given user.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getReviewAndRatingListCount($userId)
    {
        return $this
            ->getMergedReviewAndRatingList($userId)
            ->count();
    }

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId)
    {
        $reviewAndRatingList = $this->getMergedReviewAndRatingList($userId);

        return $this->sortReviewAndRatingList($reviewAndRatingList);
    }

    /**
     * Returns merged Rating and Review.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    private function getMergedReviewAndRatingList($userId)
    {
        $reviews = $this->userReviewService->getReviews($userId);
        $ratings = $this->userRatingService->getRatings($userId);

        return $this
            ->reviewAndRatingMergingService
            ->mergeReviewAndRating($reviews, $ratings);
    }

    /**
     * Sorts ReviewAndRating list.
     *
     * @param ArrayCollection $reviewAndRatingList
     *
     * @return ArrayCollection
     */
    private function sortReviewAndRatingList(ArrayCollection $reviewAndRatingList)
    {
        $reviewAndRatingListArray = $reviewAndRatingList->toArray();

        usort($reviewAndRatingListArray, function (ReviewAndRating $first, ReviewAndRating $second) {
            return $first->getCreatedAt() < $second->getCreatedAt() ? 1 : -1;
        });

        return new ArrayCollection($reviewAndRatingListArray);
    }
}
