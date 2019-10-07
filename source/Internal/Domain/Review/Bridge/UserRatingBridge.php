<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Exception\RatingPermissionException;
use OxidEsales\Eshop\Application\Model\Rating;

class UserRatingBridge implements UserRatingBridgeInterface
{
    /**
     * @var UserRatingServiceInterface
     */
    private $userRatingService;

    /**
     * UserRatingBridge constructor.
     *
     * @param UserRatingServiceInterface $userRatingService
     */
    public function __construct(
        UserRatingServiceInterface $userRatingService
    ) {
        $this->userRatingService = $userRatingService;
    }

    /**
     * Delete a Rating.
     *
     * @param string $userId
     * @param string $ratingId
     *
     * @throws RatingPermissionException
     * @throws EntryDoesNotExistDaoException
     */
    public function deleteRating($userId, $ratingId)
    {
        $rating = $this->getRatingById($ratingId);

        $this->validateUserPermissionsToManageRating($rating, $userId);

        $rating = $this->disableSubShopDeleteProtectionForRating($rating);
        $rating->delete();
    }

    /**
     * @param Rating $rating
     *
     * @return Rating
     */
    private function disableSubShopDeleteProtectionForRating(Rating $rating)
    {
        $rating->setIsDerived(false);

        return $rating;
    }

    /**
     * @param Rating $rating
     * @param string $userId
     *
     * @throws RatingPermissionException
     */
    private function validateUserPermissionsToManageRating(Rating $rating, $userId)
    {
        if ($rating->oxratings__oxuserid->value !== $userId) {
            throw new RatingPermissionException();
        }
    }

    /**
     * @param string $ratingId
     *
     * @return Rating
     * @throws EntryDoesNotExistDaoException
     */
    private function getRatingById($ratingId)
    {
        $rating = oxNew(Rating::class);
        $doesRatingExist = $rating->load($ratingId);

        if (!$doesRatingExist) {
            throw new EntryDoesNotExistDaoException();
        }

        return $rating;
    }
}
