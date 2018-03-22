<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Review\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\ServiceFactory\ReviewServiceFactory;

class ReviewServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUserReviewAndRatingBridge()
    {
        $reviewServiceFactory = new ReviewServiceFactory();
        $userReviewAndRatingBridge = $reviewServiceFactory->getUserReviewAndRatingBridge();

        $this->assertInstanceOf(
            UserReviewAndRatingBridgeInterface::class,
            $userReviewAndRatingBridge
        );
    }

    public function testGetProductRatingBridge()
    {
        $reviewServiceFactory = new ReviewServiceFactory();
        $productRatingBridge = $reviewServiceFactory->getProductRatingBridge();

        $this->assertInstanceOf(
            ProductRatingBridgeInterface::class,
            $productRatingBridge
        );
    }

    public function testGetUserRatingBridge()
    {
        $reviewServiceFactory = new ReviewServiceFactory();

        $this->assertInstanceOf(
            UserRatingBridgeInterface::class,
            $reviewServiceFactory->getUserRatingBridge()
        );
    }

    public function testGetUserReviewBridge()
    {
        $reviewServiceFactory = new ReviewServiceFactory();

        $this->assertInstanceOf(
            UserReviewBridgeInterface::class,
            $reviewServiceFactory->getUserReviewBridge()
        );
    }
}
