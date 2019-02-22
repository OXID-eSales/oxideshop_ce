<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application\Events;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Facts;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShopAwareEventsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    public function setUp()
    {
        $context = $this->getMockBuilder(BasicContext::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getSourcePath',
                'getCommunityEditionSourcePath',
                'getConfigurationDirectoryPath',
                'getGeneratedServicesFilePath',
            ])->getMock();

        $context->method('getCommunityEditionSourcePath')->willReturn(
            (new Facts)->getCommunityEditionSourcePath()
        );
        $context->method('getGeneratedServicesFilePath')->willReturn(__DIR__ . '/generated_project.yaml');
        $context->method('getConfigurationDirectoryPath')->willReturn(__DIR__);

        $builder = $this->makeContainerBuilder($context);
        $this->container = $builder->getContainer();
        $definition = $this->container->getDefinition(ContextInterface::class);
        $definition->setClass(ContextStub::class);

        $this->container->compile();

        $this->dispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    /**
     * All three subscribers are active for shop 1, current shop is 1
     * but propagation is stopped after the second handler, so
     * we should have 2 active event handlers
     */
    public function testShopActivatedEvent()
    {
        /**
         * @var $event TestEvent
         */
        $event = $this->dispatcher->dispatch('oxidesales.testevent', new TestEvent());
        $this->assertEquals(2, $event->getNumberOfActiveHandlers());
    }

    /**
     * Only the second and third subscriber are active for shop 2, current shop is 2
     * but propagation is stopped after the second handler, so
     * we should have 1 active event handler
     */
    public function testShopNotActivatedEvent()
    {
        /**
         * @var ContextStub $contextStub
         */
        $contextStub = $this->container->get(ContextInterface::class);
        $contextStub->setCurrentShopId(2);
        /**
         * @var $event TestEvent
         */
        $event = $this->dispatcher->dispatch('oxidesales.testevent', new TestEvent());
        $this->assertEquals(1, $event->getNumberOfActiveHandlers());
    }

    /**
     * @param BasicContext $context
     * @return ContainerBuilder
     */
    private function makeContainerBuilder(BasicContext $context): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder($context);
        return $containerBuilder;
    }
}
