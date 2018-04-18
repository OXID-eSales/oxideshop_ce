<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Review\ServiceFactory\ReviewServiceFactory;
use OxidEsales\EshopCommunity\Internal\Utility\Context;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

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
     * @var LoggerServiceFactory
     */
    private $loggerServiceFactory;

    /**
     * @var ContextInterface
     */
    private $context;

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
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this
            ->getLoggerServiceFactory()
            ->getLogger();
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

    /**
     * @return LoggerServiceFactory
     */
    private function getLoggerServiceFactory()
    {
        if (!$this->loggerServiceFactory) {
            $this->loggerServiceFactory = new LoggerServiceFactory(
                $this->getContext()
            );
        }

        return $this->loggerServiceFactory;
    }

    /**
     * @return ContextInterface
     */
    private function getContext()
    {
        if (!$this->context) {
            $this->context = new Context(
                Registry::getConfig()
            );
        }

        return $this->context;
    }
}
