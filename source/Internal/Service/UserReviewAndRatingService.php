<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewAndRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class UserReviewAndRatingService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
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
     * UserReviewAndRatingFacade constructor.
     *
     * @param UserReviewServiceInterface             $userReviewService
     * @param UserRatingServiceInterface             $userRatingService
     * @param ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
     */
    public function __construct(
        UserReviewServiceInterface $userReviewService,
        UserRatingServiceInterface $userRatingService,
        ReviewAndRatingMergingServiceInterface $reviewAndRatingMergingService
    ) {
        $this->userReviewService = $userReviewService;
        $this->userRatingService = $userRatingService;
        $this->reviewAndRatingMergingService = $reviewAndRatingMergingService;
    }

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     * @param int    $itemsPerPage
     * @param int    $offset
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId, $itemsPerPage, $offset)
    {
        $reviewAndRatingList = $this->getMergedReviewAndRatingList($userId);
        $reviewAndRatingList = $this->sortReviewAndRatingList($reviewAndRatingList);
        $reviewAndRatingList = $this->paginateReviewAndRatingList(
            $reviewAndRatingList,
            $itemsPerPage,
            $offset
        );

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
        $iterator = $reviewAndRatingList->getIterator();

        $iterator->uasort(function ($first, $second) {
            return $first->getCreatedAt() < $second->getCreatedAt() ? 1 : -1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * Paginate ReviewAndRating list.
     *
     * @param ArrayCollection $reviewAndRatingList
     * @param int             $itemsCount
     * @param int             $offset
     *
     * @return mixed
     */
    private function paginateReviewAndRatingList(ArrayCollection $reviewAndRatingList, $itemsCount, $offset)
    {
        return $reviewAndRatingList->slice($offset, $itemsCount);
    }
}
