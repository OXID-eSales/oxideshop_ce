<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ReviewDaoInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Dao
 */
interface ReviewDaoInterface
{
    /**
     * Returns User Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviewsByUserId($userId);
}
