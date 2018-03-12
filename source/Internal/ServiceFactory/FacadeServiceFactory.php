<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Facade\ProductRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewAndRatingFacadeInterface;

/**
 * Class FacadeServiceFactory
 * @internal
 * @package OxidEsales\EshopCommunity\Internal
 */
class FacadeServiceFactory
{
    /**
     * @var FacadeServiceFactory
     */
    private static $instance;

    /**
     * @var ReviewServiceFactory
     */
    private $reviewServiceFactory;

    /**
     * FacadeServiceFactory constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return FacadeServiceFactory
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return UserReviewAndRatingFacadeInterface
     */
    public function getUserReviewAndRatingFacade()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserReviewAndRatingFacade();
    }

    /**
     * @return ProductRatingFacadeInterface
     */
    public function getProductRatingFacade()
    {
        return $this
            ->getReviewServiceFactory()
            ->getProductRatingFacade();
    }

    /**
     * @return \OxidEsales\EshopCommunity\Internal\Facade\UserRatingFacade
     */
    public function getUserRatingFacade()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserRatingFacade();
    }

    /**
     * @return \OxidEsales\EshopCommunity\Internal\Facade\UserReviewFacade
     */
    public function getUserReviewFacade()
    {
        return $this
            ->getReviewServiceFactory()
            ->getUserReviewFacade();
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
