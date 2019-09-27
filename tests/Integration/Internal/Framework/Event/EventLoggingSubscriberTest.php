<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ServicesYamlConfigurationErrorEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\TestingLibrary\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventLoggingSubscriberTest extends UnitTestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
    private $container;

    private $testlog = __DIR__ . DIRECTORY_SEPARATOR . 'test.log';

    public function setUp()
    {
        $containerBuilder = new ContainerBuilder(new BasicContextStub());
        $this->container = $containerBuilder->getContainer();
        $contextDefinition = $this->container->getDefinition(ContextInterface::class);
        $contextDefinition->setClass(ContextStub::class);
        $this->container->compile();
    }

    public function tearDown()
    {
        if (file_exists($this->testlog)) {
            unlink($this->testlog);
        }
    }

    public function testLoggingOnConfigurationErrorEvent()
    {
        /** @var ContextStub $context */
        $context = $this->container->get(ContextInterface::class);
        $context->setLogFilePath(__dir__ . DIRECTORY_SEPARATOR . 'test.log');

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(
            ServicesYamlConfigurationErrorEvent::NAME,
            new ServicesYamlConfigurationErrorEvent('error', 'just/some/path/services.yaml')
        );

        $this->assertTrue(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'test.log'));
    }
}
