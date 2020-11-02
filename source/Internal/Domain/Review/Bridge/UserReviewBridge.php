<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Exception\ReviewPermissionException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;

class UserReviewBridge implements UserReviewBridgeInterface
{
    /**
     * @var UserReviewServiceInterface
     */
    private $userReviewService;

    /**
     * UserReviewBridge constructor.
     */
    public function __construct(
        UserReviewServiceInterface $userReviewService
    ) {
        $this->userReviewService = $userReviewService;
    }

    /**
     * Delete a Review.
     *
     * @param string $userId
     * @param string $reviewId
     *
     * @throws ReviewPermissionException
     * @throws EntryDoesNotExistDaoException
     */
    public function deleteReview($userId, $reviewId): void
    {
        $review = $this->getReviewById($reviewId);

        $this->validateUserPermissionsToManageReview($review, $userId);

        $review->delete();
    }

    /**
     * @param string $userId
     *
     * @throws ReviewPermissionException
     */
    private function validateUserPermissionsToManageReview(Review $review, $userId): void
    {
        if ($review->oxreviews__oxuserid->value !== $userId) {
            throw new ReviewPermissionException();
        }
    }

    /**
     * @param string $reviewId
     *
     * @return Review
     *
     * @throws EntryDoesNotExistDaoException
     */
    private function getReviewById($reviewId)
    {
        $review = oxNew(Review::class);
        $doesReviewExist = $review->load($reviewId);

        if (!$doesReviewExist) {
            throw new EntryDoesNotExistDaoException();
        }

        return $review;
    }
}
