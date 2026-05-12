<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class BootstrapContainerBuilder
{
    public function create(): ContainerBuilder
    {
        $symfonyContainer = new ContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());

        $symfonyContainer->register(EventDispatcherInterface::class, EventDispatcher::class)->setPublic(true);
        $symfonyContainer->setAlias('event_dispatcher', EventDispatcherInterface::class)->setPublic(true);

        $loader = new YamlFileLoader($symfonyContainer, new FileLocator(__DIR__));
        $loader->load('bootstrap-services.yaml');

        return $symfonyContainer;
    }
}
