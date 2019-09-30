<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
class TestContainerFactory
{
    /**
     * @var BasicContextStub
     */
    private $context;

    public function __construct()
    {
        $this->prepareVFS();
        $this->context = $this->getBasicContextStub();
        $this->context = $this->getContextStub();
    }

    public function create(): SymfonyContainerBuilder
    {
        $containerBuilder = new ContainerBuilder($this->context);

        $container = $containerBuilder->getContainer();
        $container = $this->setAllServicesAsPublic($container);
        $container = $this->setBasicContextStub($container);
        $container = $this->setContextStub($container);

        return $container;
    }

    private function setAllServicesAsPublic(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        return $container;
    }

    private function setBasicContextStub(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        $container->set(BasicContextInterface::class, $this->context);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);

        return $container;
    }

    private function setContextStub(SymfonyContainerBuilder $container): SymfonyContainerBuilder
    {
        $container->set(ContextInterface::class, $this->context);
        $container->autowire(ContextInterface::class, ContextStub::class);

        return $container;
    }

    private function getBasicContextStub(): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setProjectConfigurationDirectory($this->getTestProjectConfigurationDirectory());

        return $context;
    }

    private function getContextStub(): ContextStub
    {
        $context = new ContextStub();
        $context->setProjectConfigurationDirectory($this->getTestProjectConfigurationDirectory());

        return $context;
    }

    private function prepareVFS(): void
    {
        $vfsStreamDirectory = vfsStream::setup('shops');
        vfsStream::create([], $vfsStreamDirectory);
    }

    private function getTestProjectConfigurationDirectory(): string
    {
        return vfsStream::url('shops/');
    }
}
