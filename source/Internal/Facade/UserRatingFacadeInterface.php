<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

/**
 * Interface UserRatingFacadeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
interface UserRatingFacadeInterface
{
    /**
     * Delete a Rating.
     *
     * @param string $userId
     * @param string $ratingId
     *
     * @return bool
     */
    public function deleteRating($userId, $ratingId);
}
