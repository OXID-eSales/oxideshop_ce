<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application;

use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance\ContainerWrapper;
use OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance\NotFoundException;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp() {

        if (file_exists(ContainerFactory::$containerCache)){
            unlink(ContainerFactory::$containerCache);
        }

        // Ensure that we always have a new instance
        $class = new \ReflectionClass(ContainerFactory::class);
        $factory = $class->newInstanceWithoutConstructor();
        $this->container = $factory->getContainer();

    }

    public function tearDown()
    {
        if (file_exists(ContainerFactory::$containerCache)){
            unlink(ContainerFactory::$containerCache);
        }
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

    /**
     * Checks that a private service may not be accessed
     */
    public function testPrivateServices() {

        $this->setExpectedException(NotFoundExceptionInterface::class);

        $this->container->get(Logger::class);
    }

    /**
     * Checks that the cachefile is used if it exists
     */
    public function testCacheIsUsed() {

        // Prepare the dummy cache
        $cachedummy = <<<EOT
<?php

use Symfony\Component\DependencyInjection\Container;

class ProjectServiceContainer extends Container
{
    public function get(\$id, \$invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE) {
        return "This is a dummy container";
    }
}
EOT;
        file_put_contents(ContainerFactory::$containerCache, $cachedummy);

        // Fetch a new instance of the container
        $class = new \ReflectionClass(ContainerFactory::class);
        $factory = $class->newInstanceWithoutConstructor();
        $container = $factory->getContainer();

        $this->assertEquals("This is a dummy container", $container->get(LoggerInterface::class));

    }

    /**
     * Checks that the cachefile has been created
     */
    public function testCacheIsCreated() {

        $this->assertTrue(file_exists(ContainerFactory::$containerCache));

    }

}
