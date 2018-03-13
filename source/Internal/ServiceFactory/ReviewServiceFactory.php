<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ServiceFactory;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Dao\RatingDao;
use OxidEsales\EshopCommunity\Internal\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\ReviewDao;
use OxidEsales\EshopCommunity\Internal\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewAndRatingFacade;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewAndRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Service\ReviewAndRatingMergingService;
use OxidEsales\EshopCommunity\Internal\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Service\UserRatingService;
use OxidEsales\EshopCommunity\Internal\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Service\UserReviewAndRatingService;
use OxidEsales\EshopCommunity\Internal\Service\UserReviewAndRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Service\UserReviewService;
use OxidEsales\EshopCommunity\Internal\Service\UserReviewServiceInterface;

/**
 * Class ReviewServiceFactory
 * @internal
 * @package OxidEsales\EshopCommunity\Internal
 */
class ReviewServiceFactory
{
    /**
     * @var UserReviewAndRatingFacadeInterface
     */
    private $userReviewAndRatingFacade;

    /**
     * @var UserReviewAndRatingServiceInterface
     */
    private $userReviewAndRatingService;

    /**
     * @var UserReviewServiceInterface
     */
    private $userReviewService;

    /**
     * @var UserRatingServiceInterface
     */
    private $userRatingService;

    /**
     * @var ReviewAndRatingMergingServiceInterface
     */
    private $reviewAndRatingMergingService;

    /**
     * @var ReviewDaoInterface
     */
    private $reviewDao;

    /**
     * @var RatingDaoInterface
     */
    private $ratingDao;

    /**
     * @return UserReviewAndRatingFacadeInterface
     */
    public function getUserReviewAndRatingFacade()
    {
        if (!$this->userReviewAndRatingFacade) {
            $this->userReviewAndRatingFacade = new UserReviewAndRatingFacade(
                $this->getUserReviewAndRatingService()
            );
        }

        return $this->userReviewAndRatingFacade;
    }

    /**
     * @return UserReviewAndRatingServiceInterface
     */
    private function getUserReviewAndRatingService()
    {
        if (!$this->userReviewAndRatingService) {
            $this->userReviewAndRatingService = new UserReviewAndRatingService(
                $this->getUserReviewService(),
                $this->getUserRatingService(),
                $this->getReviewAndRatingMergingService()
            );
        }

        return $this->userReviewAndRatingService;
    }

    /**
     * @return UserReviewServiceInterface
     */
    private function getUserReviewService()
    {
        if (!$this->userReviewService) {
            $this->userReviewService = new UserReviewService(
                $this->getReviewDao()
            );
        }

        return $this->userReviewService;
    }

    /**
     * @return ReviewDaoInterface
     */
    private function getReviewDao()
    {
        if (!$this->reviewDao) {
            $this->reviewDao = new ReviewDao(
                $this->getDatabase()
            );
        }

        return $this->reviewDao;
    }

    /**
     * @return UserRatingServiceInterface
     */
    private function getUserRatingService()
    {
        if (!$this->userRatingService) {
            $this->userRatingService = new UserRatingService(
                $this->getRatingDao()
            );
        }

        return $this->userRatingService;
    }

    /**
     * @return RatingDaoInterface
     */
    private function getRatingDao()
    {
        if (!$this->ratingDao) {
            $this->ratingDao = new RatingDao(
                $this->getDatabase()
            );
        }

        return $this->ratingDao;
    }

    /**
     * @return ReviewAndRatingMergingServiceInterface
     */
    private function getReviewAndRatingMergingService()
    {
        if (!$this->reviewAndRatingMergingService) {
            $this->reviewAndRatingMergingService = new ReviewAndRatingMergingService();
        }

        return $this->reviewAndRatingMergingService;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    private function getDatabase()
    {
        return DatabaseProvider::getDb();
    }
}
