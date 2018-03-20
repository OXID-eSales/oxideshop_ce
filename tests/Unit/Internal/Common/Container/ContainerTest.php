<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Common\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Common\Container\Container;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstance()
    {
        $container = Container::getInstance();
        $this->assertInstanceOf(
            Container::class,
            $container
        );
    }

    public function testGetUserReviewAndRatingBridge()
    {
        $container = Container::getInstance();

        $this->assertInstanceOf(
            UserReviewAndRatingBridgeInterface::class,
            $container->getUserReviewAndRatingBridge()
        );
    }

    public function testGetProductRatingBridge()
    {
        $container = Container::getInstance();

        $this->assertInstanceOf(
            ProductRatingBridgeInterface::class,
            $container->getProductRatingBridge()
        );
    }

    public function testGetUserRatingBridge()
    {
        $container = Container::getInstance();

        $this->assertInstanceOf(
            UserRatingBridgeInterface::class,
            $container->getUserRatingBridge()
        );
    }

    public function testGetUserReviewBridge()
    {
        $container = Container::getInstance();

        $this->assertInstanceOf(
            UserReviewBridgeInterface::class,
            $container->getUserReviewBridge()
        );
    }
}
