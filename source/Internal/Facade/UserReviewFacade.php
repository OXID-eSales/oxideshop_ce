<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Exception\ReviewPermissionException;
use OxidEsales\Eshop\Application\Model\Review;

/**
 * Class UserReviewFacade
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
class UserReviewFacade implements UserReviewFacadeInterface
{
    /**
     * @var UserReviewServiceInterface
     */
    private $userReviewService;

    /**
     * UserReviewFacade constructor.
     *
     * @param UserReviewServiceInterface $userReviewService
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
     */
    public function deleteReview($userId, $reviewId)
    {
        $review = oxNew(Review::class);
        $review->load($reviewId);

        if ($review->oxreviews__oxuserid->value !== $userId) {
            throw new ReviewPermissionException();
        }
        $review->delete($reviewId);
    }
}
