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
 */
interface UserRatingServiceInterface
{
    /**
     * Returns Ratings of User.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatings($userId);
}
