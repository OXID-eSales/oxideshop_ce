<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application;

use Monolog\Logger;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Events\ShopAwareEventDispatcher;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewAndRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        if (file_exists($this->getCacheFilePath())){
            unlink($this->getCacheFilePath());
        }

        // Ensure that we always have a new instance
        $class = new \ReflectionClass(ContainerFactory::class);
        $factory = $class->newInstanceWithoutConstructor();
        $this->container = $factory->getContainer();
    }

    public function tearDown()
    {
        if (file_exists($this->getCacheFilePath())){
            unlink($this->getCacheFilePath());
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
    public function testConfiguration($interface)
    {
        $this->assertInstanceOf(
            $interface,
            $this->container->get($interface)
        );
    }

    public function interfaceProvider()
    {
        return [
            [UserReviewAndRatingBridgeInterface::class],
            [ProductRatingBridgeInterface::class],
            [UserRatingBridgeInterface::class],
            [UserReviewBridgeInterface::class],
            [LoggerInterface::class],
            [ContactFormBridgeInterface::class],
        ];
    }

    /**
     * Checks that a private service may not be accessed
     */
    public function testPrivateServices()
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $this->container->get(Logger::class);
    }

    /**
     * Checks that the cachefile is used if it exists
     */
    public function testCacheIsUsed()
    {
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
        file_put_contents($this->getCacheFilePath(), $cachedummy);
        $dummyCopy = file_get_contents($this->getCacheFilePath());
        $this->assertEquals($cachedummy, $dummyCopy);

        // Fetch a new instance of the container
        $class = new \ReflectionClass(ContainerFactory::class);
        $factory = $class->newInstanceWithoutConstructor();
        $container = $factory->getContainer();

        $this->assertEquals("This is a dummy container", $container->get(LoggerInterface::class));

    }

    /**
     * Checks that the cachefile has been created
     */
    public function testCacheIsCreated()
    {
        $this->assertTrue(file_exists($this->getCacheFilePath()));
    }

    /**
     * Tests that an event dispatcher is available and implements
     * the correct interface.
     *
     */
    public function testEventDispatcher()
    {
        $this->assertInstanceOf(ShopAwareEventDispatcher::class, $this->container->get('event_dispatcher'));
    }

    /**
     * @return string
     */
    private function getCacheFilePath()
    {
        $compileDir = Registry::getConfig()->getConfigParam('sCompileDir');

        return $compileDir . '/containercache.php';
    }
}
