<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Facade\ProductRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewAndRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewFacadeInterface;
use OxidEsales\EshopCommunity\Internal\ServiceFactory\ReviewServiceFactory;

class ReviewServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUserReviewAndRatingFacade()
    {
        $reviewServiceFactory = new ReviewServiceFactory();
        $userReviewAndRatingFacade = $reviewServiceFactory->getUserReviewAndRatingFacade();

        $this->assertInstanceOf(
            UserReviewAndRatingFacadeInterface::class,
            $userReviewAndRatingFacade
        );
    }

    public function testGetProductRatingFacade()
    {
        $reviewServiceFactory = new ReviewServiceFactory();
        $productRatingFacade = $reviewServiceFactory->getProductRatingFacade();

        $this->assertInstanceOf(
            ProductRatingFacadeInterface::class,
            $productRatingFacade
        );
    }

    public function testGetUserRatingFacade()
    {
        $reviewServiceFactory = new ReviewServiceFactory();

        $facade = $reviewServiceFactory->getUserRatingFacade();

        $this->assertInstanceOf(
            UserRatingFacadeInterface::class,
            $reviewServiceFactory->getUserRatingFacade()
        );
    }

    public function testGetUserReviewFacade()
    {
        $reviewServiceFactory = new ReviewServiceFactory();

        $this->assertInstanceOf(
            UserReviewFacadeInterface::class,
            $reviewServiceFactory->getUserReviewFacade()
        );
    }
}
