<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingServiceInterface as EshopReviewAndRatingMergingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewAndRatingServiceInterface as EshopUserReviewAndRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class UserReviewAndRatingService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
class UserReviewAndRatingService implements EshopUserReviewAndRatingServiceInterface
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
     * UserReviewAndRatingFacade constructor.
     *
     * @param UserReviewServiceInterface             $userReviewService
     * @param UserRatingServiceInterface             $userRatingService
     * @param ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
     */
    public function __construct(
        UserReviewServiceInterface $userReviewService,
        UserRatingServiceInterface $userRatingService,
        EshopReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
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

        usort($reviewAndRatingListArray, function ($first, $second) {
            return $first->getCreatedAt() < $second->getCreatedAt() ? 1 : -1;
        });

        return new ArrayCollection($reviewAndRatingListArray);
    }
}
