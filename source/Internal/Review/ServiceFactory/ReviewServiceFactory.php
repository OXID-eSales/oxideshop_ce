<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Review\ServiceFactory;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Common\DataMapper\IdentifiableObjectMapperInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ProductRatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\RatingDao;
use OxidEsales\EshopCommunity\Internal\Review\Dao\RatingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDao;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataMapper\ProductRatingDataMapper;
use OxidEsales\EshopCommunity\Internal\Review\DataMapper\RatingDataMapper;
use OxidEsales\EshopCommunity\Internal\Review\DataMapper\ReviewDataMapper;
use OxidEsales\EshopCommunity\Internal\Review\Service\ProductRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\ProductRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\RatingCalculatorService;
use OxidEsales\EshopCommunity\Internal\Review\Service\RatingCalculatorServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\ReviewAndRatingMergingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewAndRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewAndRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewService;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewServiceInterface;

/**
 * @internal
 */
class ReviewServiceFactory
{
    /**
     * @var UserReviewAndRatingBridgeInterface
     */
    private $userReviewAndRatingBridge;

    /**
     * @var ProductRatingBridgeInterface
     */
    private $productRatingBridge;

    /**
     * @var UserReviewBridgeInterface
     */
    private $userReviewBridge;

    /**
     * @var UserRatingBridgeInterface
     */
    private $userRatingBridge;

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
     * @var ProductRatingServiceInterface
     */
    private $productRatingService;

    /**
     * @var ReviewDaoInterface
     */
    private $reviewDao;

    /**
     * @var RatingDaoInterface
     */
    private $ratingDao;

    /**
     * @var ProductRatingDaoInterface
     */
    private $productRatingDao;

    /**
     * @var RatingCalculatorServiceInterface
     */
    private $ratingCalculator;

    /**
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;

    /**
     * @var IdentifiableObjectMapperInterface
     */
    private $ratingDataMapper;

    /**
     * @var IdentifiableObjectMapperInterface
     */
    private $reviewDataMapper;

    /**
     * @var IdentifiableObjectMapperInterface
     */
    private $productRatingDataMapper;

    /**
     * @return UserReviewAndRatingBridgeInterface
     */
    public function getUserReviewAndRatingBridge()
    {
        if (!$this->userReviewAndRatingBridge) {
            $this->userReviewAndRatingBridge = new UserReviewAndRatingBridge(
                $this->getUserReviewAndRatingService()
            );
        }

        return $this->userReviewAndRatingBridge;
    }

    /**
     * @return ProductRatingBridgeInterface
     */
    public function getProductRatingBridge()
    {
        if (!$this->productRatingBridge) {
            $this->productRatingBridge = new ProductRatingBridge(
                $this->getProductRatingService()
            );
        }

        return $this->productRatingBridge;
    }

    /**
     * @return UserRatingBridge
     */
    public function getUserRatingBridge()
    {
        if (!$this->userRatingBridge) {
            $this->userRatingBridge = new UserRatingBridge(
                $this->getUserRatingService()
            );
        }

        return $this->userRatingBridge;
    }

    /**
     * @return UserReviewBridge
     */
    public function getUserReviewBridge()
    {
        if (!$this->userReviewBridge) {
            $this->userReviewBridge = new UserReviewBridge(
                $this->getUserReviewService()
            );
        }

        return $this->userReviewBridge;
    }

    /**
     * @return ProductRatingDaoInterface
     */
    public function getProductRatingDao()
    {
        if (!$this->productRatingDao) {
            $this->productRatingDao = new ProductRatingDao(
                $this->getQueryBuilderFactory(),
                $this->getProductRatingDataMapper()
            );
        }

        return $this->productRatingDao;
    }

    /**
     * @return ReviewDaoInterface
     */
    public function getReviewDao()
    {
        if (!$this->reviewDao) {
            $this->reviewDao = new ReviewDao(
                $this->getQueryBuilderFactory(),
                $this->getReviewDataMapper()
            );
        }

        return $this->reviewDao;
    }

    /**
     * @return RatingDaoInterface
     */
    public function getRatingDao()
    {
        if (!$this->ratingDao) {
            $this->ratingDao = new RatingDao(
                $this->getQueryBuilderFactory(),
                $this->getRatingDataMapper()
            );
        }

        return $this->ratingDao;
    }

    /**
     * @return ReviewDataMapper
     */
    private function getReviewDataMapper()
    {
        if (!$this->reviewDataMapper) {
            $this->reviewDataMapper = new ReviewDataMapper();
        }

        return $this->reviewDataMapper;
    }

    /**
     * @return RatingDataMapper
     */
    private function getRatingDataMapper()
    {
        if (!$this->ratingDataMapper) {
            $this->ratingDataMapper = new RatingDataMapper();
        }

        return $this->ratingDataMapper;
    }

    /**
     * @return ProductRatingDataMapper
     */
    private function getProductRatingDataMapper()
    {
        if (!$this->productRatingDataMapper) {
            $this->productRatingDataMapper = new ProductRatingDataMapper();
        }

        return $this->productRatingDataMapper;
    }

    /**
     * @return ProductRatingServiceInterface
     */
    private function getProductRatingService()
    {
        if (!$this->productRatingService) {
            $this->productRatingService = new ProductRatingService(
                $this->getRatingDao(),
                $this->getProductRatingDao(),
                $this->getRatingCalculator()
            );
        }

        return $this->productRatingService;
    }

    /**
     * @return RatingCalculatorServiceInterface
     */
    private function getRatingCalculator()
    {
        if (!$this->ratingCalculator) {
            $this->ratingCalculator = new RatingCalculatorService();
        }

        return $this->ratingCalculator;
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
     * @return QueryBuilderFactory
     */
    private function getQueryBuilderFactory()
    {
        if (!$this->queryBuilderFactory) {
            $this->queryBuilderFactory = new QueryBuilderFactory(
                $this->getConnection()
            );
        }

        return $this->queryBuilderFactory;
    }

    /**
     * @return Connection
     */
    private function getConnection()
    {
        $database = DatabaseProvider::getDb();
        $r = new \ReflectionMethod(Database::class, 'getConnection');
        $r->setAccessible(true);

        return $r->invoke($database);
    }
}
