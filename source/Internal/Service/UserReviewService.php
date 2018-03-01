<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Internal\Dao\ReviewDaoInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;

/**
 * Class UserReviewService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
class UserReviewService implements UserReviewServiceInterface
{
    /**
     * @var ReviewDaoInterface
     */
    private $reviewDao;

    /**
     * UserReviewService constructor.
     * @param ReviewDaoInterface $reviewDao
     */
    public function __construct(ReviewDaoInterface $reviewDao)
    {
        $this->reviewDao = $reviewDao;
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
