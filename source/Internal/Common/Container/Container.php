<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Common\Container;

use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Review\ServiceFactory\ReviewServiceFactory;

/**
 * Class Container
 * @internal
 * @package OxidEsales\EshopCommunity\Internal
 */
class Container
{
    /**
     * @var Container
     */
    private static $instance;

    /**
     * @var ReviewServiceFactory
     */
    private $reviewServiceFactory;

    /**
     * Container constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return Container
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return UserReviewAndRatingBridgeInterface
     */
    public function getUserReviewAndRatingBridge()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserReviewAndRatingBridge();
    }

    /**
     * @return ProductRatingBridgeInterface
     */
    public function getProductRatingBridge()
    {
        return $this
            ->getReviewServiceFactory()
            ->getProductRatingBridge();
    }

    /**
     * @return UserRatingBridge
     */
    public function getUserRatingBridge()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserRatingBridge();
    }

    /**
     * @return UserReviewBridge
     */
    public function getUserReviewBridge()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserReviewBridge();
    }

    /**
     * @return ReviewServiceFactory
     */
    private function getReviewServiceFactory()
    {
        if (!$this->reviewServiceFactory) {
            $this->reviewServiceFactory = new ReviewServiceFactory();
        }

        return $this->reviewServiceFactory;
    }
}
