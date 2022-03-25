<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ReviewDaoInterface;

class UserReviewService implements UserReviewServiceInterface
{
    /**
     * UserReviewService constructor.
     */
    public function __construct(private ReviewDaoInterface $reviewDao)
    {
    }

    /**
     * Returns User Reviews.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getReviews($userId)
    {
        return $this->reviewDao->getReviewsByUserId($userId);
    }
}
