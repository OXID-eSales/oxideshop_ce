<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp() {

        $this->container = ContainerFactory::getContainer();

    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            $this->container
        );
    }

    /**
     * @dataProvider interfaceProvider
     *
     * @param $interface
     */
    public function testConfiguration($interface) {
        $this->assertInstanceOf($interface, $this->container->get($interface));
    }

    public function interfaceProvider() {

        return [[UserReviewAndRatingBridgeInterface::class],
                [ProductRatingBridgeInterface::class],
                [UserRatingBridgeInterface::class],
                [UserReviewBridgeInterface::class],
                [LoggerInterface::class]];

    }

}
