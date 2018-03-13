<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ServiceFactory;

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
     * @var UserReviewAndRatingFacadeInterface
     */
    private $userReviewAndRatingFacade;

    /**
     * @var ReviewServiceFactory
     */
    private $reviewServiceFactory;

    /**
     * FacadeServiceFactory constructor.
     */
    public function __construct()
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
        if (!$this->userReviewAndRatingFacade) {
            $this->userReviewAndRatingFacade = $this
                ->getReviewServiceFactory()
                ->getUserReviewAndRatingFacade();
        }

        return $this->userReviewAndRatingFacade;
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
