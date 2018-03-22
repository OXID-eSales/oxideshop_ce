<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Review\ViewDataObject\ReviewAndRating;

/**
 * Class UserReviewAndRatingService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Service
 */
class UserReviewAndRatingService implements UserReviewAndRatingServiceInterface
{
    /**
     * @var UserReviewServiceInterface
     */
    private $userReviewService;

    /**
     * @var UserRatingServiceInterface
     */
    private $userRatingService;

    /**
     * @var ReviewAndRatingMergingServiceInterface
     */
    private $reviewAndRatingMergingService;

    /**
     * UserReviewAndRatingBridge constructor.
     *
     * @param UserReviewServiceInterface             $userReviewService
     * @param UserRatingServiceInterface             $userRatingService
     * @param ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
     */
    public function __construct(
        UserReviewServiceInterface              $userReviewService,
        UserRatingServiceInterface              $userRatingService,
        ReviewAndRatingMergingServiceInterface  $reviewAndRatingMergingService
    ) {
        $this->userReviewService = $userReviewService;
        $this->userRatingService = $userRatingService;
        $this->reviewAndRatingMergingService = $reviewAndRatingMergingService;
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
        $reviewAndRatingList = $this->sortReviewAndRatingList($reviewAndRatingList);

        return $reviewAndRatingList;
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
