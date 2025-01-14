<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

class TestContainerFactory
{
    public function create(): SymfonyContainerBuilder
    {
        $contextStub = new ContextStub();
        $container = (new ContainerBuilder($contextStub))
            ->getContainer();

        $container->set(ContextInterface::class, $contextStub);
        $container->set(BasicContextInterface::class, $contextStub);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);
        $container->autowire(ContextInterface::class, ContextStub::class);

        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        return $container;
    }
}
