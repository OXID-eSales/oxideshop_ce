<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

use OxidEsales\Eshop\Internal\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Exception\RatingPermissionException;
use OxidEsales\Eshop\Application\Model\Rating;

/**
 * Class UserRatingFacade
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
class UserRatingFacade implements UserRatingFacadeInterface
{
    /**
     * @var UserRatingServiceInterface
     */
    private $userRatingService;

    /**
     * UserRatingFacade constructor.
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
     */
    public function deleteRating($userId, $ratingId)
    {
        $rating = oxNew(Rating::class);
        $rating->load($ratingId);

        if ($rating->oxratings__oxuserid->value !== $userId) {
            throw new RatingPermissionException();
        }

        $rating->delete($ratingId);
    }
}
