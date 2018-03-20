<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Bridge;

/**
 * Interface UserRatingBridgeInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Review\Bridge
 */
interface UserRatingBridgeInterface
{
    /**
     * Delete a Rating.
     *
     * @param string $userId
     * @param string $ratingId
     */
    public function deleteRating($userId, $ratingId);
}
