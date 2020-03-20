<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\RatingDaoInterface;

class UserRatingService implements UserRatingServiceInterface
{
    /**
     * @var RatingDaoInterface
     */
    private $ratingDao;

    /**
     * UserRatingService constructor.
     * @param RatingDaoInterface $ratingDao
     */
    public function __construct(RatingDaoInterface $ratingDao)
    {
        $this->ratingDao = $ratingDao;
    }

    /**
     * Returns user ratings.
     *
     * @param string $userId
     *
     * @return ArrayCollection
     */
    public function getRatings($userId)
    {
        return $this->ratingDao->getRatingsByUserId($userId);
    }
}
