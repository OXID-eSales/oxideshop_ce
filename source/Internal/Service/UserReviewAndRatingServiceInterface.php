<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserReviewAndRatingServiceInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
interface UserReviewAndRatingServiceInterface
{
    /**
     * Returns Collection of User Ratings and Reviews.
     *
     * @param string $userId
     * @param int    $itemsPerPage
     * @param int    $offset
     *
     * @return ArrayCollection
     */
    public function getReviewAndRatingList($userId, $itemsPerPage, $offset);
}
