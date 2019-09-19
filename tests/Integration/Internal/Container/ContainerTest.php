<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use Monolog\Logger;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Event\ShopAwareEventDispatcher;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
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
        ContainerFactory::resetContainer();

        $this->container = ContainerFactory::getInstance()->getContainer();
    }

    public function tearDown()
    {
        ContainerFactory::resetContainer();
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            $this->container
        );
    }

    public function testAllServicesAreCorrectlyConfigured()
    {
        $testContainer = (new TestContainerFactory())->create();
        $testContainer->compile();
        foreach ($testContainer->getDefinitions() as $key => $definition) {
            $testContainer->get($key);
        };
        $this->assertTrue(true);
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
        $this->assertFileExists($this->getCacheFilePath());
    }

    /**
     * Tests that an event dispatcher is available and implements
     * the correct interface.
     *
     */
    public function testEventDispatcher()
    {
        $this->assertInstanceOf(ShopAwareEventDispatcher::class, $this->container->get(EventDispatcherInterface::class));
    }

    /**
     * @return string
     */
    private function getCacheFilePath()
    {
        return $this->container->get(ContextInterface::class)->getContainerCacheFilePath();
    }
}
