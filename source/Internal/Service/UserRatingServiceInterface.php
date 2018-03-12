<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface UserRatingServiceInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
interface UserRatingServiceInterface
{
    /**
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatings($userId);

    public function deleteRating($userId, $ratingId);
}
