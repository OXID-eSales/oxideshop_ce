<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Event;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ServicesYamlConfigurationErrorEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventLoggingSubscriberTest extends TestCase
{
    private \Symfony\Component\DependencyInjection\ContainerBuilder $container;

    private $testlog = __DIR__ . DIRECTORY_SEPARATOR . 'test.log';

    public function setup(): void
    {
        parent::setUp();
        $containerBuilder = new ContainerBuilder(new BasicContextStub());
        $this->container = $containerBuilder->getContainer();
        $contextDefinition = $this->container->getDefinition(ContextInterface::class);
        $contextDefinition->setClass(ContextStub::class);
        $this->container->compile();
    }

    public function tearDown(): void
    {
        if (file_exists($this->testlog)) {
            unlink($this->testlog);
        }
        parent::tearDown();
    }

    public function testLoggingOnConfigurationErrorEvent(): void
    {
        /** @var ContextStub $context */
        $context = $this->container->get(ContextInterface::class);
        $context->setLogFilePath(__dir__ . DIRECTORY_SEPARATOR . 'test.log');

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(
            new ServicesYamlConfigurationErrorEvent('error', 'just/some/path/services.yaml')
        );

        self::assertFileExists(__DIR__ . DIRECTORY_SEPARATOR . 'test.log');
    }
}
