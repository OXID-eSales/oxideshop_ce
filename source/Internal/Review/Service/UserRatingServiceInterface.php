<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserRatingServiceInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Service
 */
interface UserRatingServiceInterface
{
    /**
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatings($userId);

    /**
     * @param string $userId
     * @param string $ratingId
     *
     * @return bool
     */
    public function deleteRating($userId, $ratingId);
}
