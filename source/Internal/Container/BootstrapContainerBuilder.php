<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\Env\DotEnvLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @internal
 */
class BootstrapContainerBuilder
{
    public function create(): ContainerBuilder
    {
        (new DotEnvLoader())->loadEnvironmentVariables();
        $symfonyContainer = new ContainerBuilder();
        $symfonyContainer->addCompilerPass(new RegisterListenersPass());

        $loader = new YamlFileLoader($symfonyContainer, new FileLocator(__DIR__));
        $loader->load('bootstrap-services.yaml');

        return $symfonyContainer;
    }
}
