<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass;

use OxidEsales\EshopCommunity\Internal\Framework\Api\AttributeRouteControllerLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection;

class RoutePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $loader = new AttributeRouteControllerLoader();
        $routes = new RouteCollection();

        foreach ($container->getDefinitions() as $definition) {
            if ($definition->isPublic() && class_exists($definition->getClass())) {
                $routes->addCollection($loader->load($definition->getClass()));
            }
        }

        $compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();

        $container->setParameter('oxid.routes', $compiledRoutes);
    }
}
