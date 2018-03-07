<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Facade;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserReviewAndRatingFacadeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
interface UserReviewAndRatingFacadeInterface
{
    /**
     * Get number of reviews by given user.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getReviewAndRatingListCount($userId);

    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId);
}
