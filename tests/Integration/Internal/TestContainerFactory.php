<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
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
    }

    public function create(): SymfonyContainerBuilder
    {
        $containerBuilder = new ContainerBuilder($this->context);

        $container = $containerBuilder->getContainer();
        $container = $this->setAllServicesAsPublic($container);
        $container = $this->setBasicContextStub($container);

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

    private function getBasicContextStub(): BasicContextStub
    {
        $context = new BasicContextStub();
        $context->setProjectConfigurationDirectory($this->getTestProjectConfigurationDirectory());

        return $context;
    }

    private function prepareVFS(): void
    {
        $vfsStreamDirectory = vfsStream::setup('project_configuration');
        vfsStream::create([], $vfsStreamDirectory);
    }

    private function getTestProjectConfigurationDirectory(): string
    {
        return vfsStream::url('project_configuration/');
    }
}
