<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
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
