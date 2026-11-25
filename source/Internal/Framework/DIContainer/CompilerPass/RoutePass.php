<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\ReservedRoutePathException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\AttributeRouteControllerLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutePass implements CompilerPassInterface
{
    /**
     * @throws ReservedRoutePathException
     */
    public function process(ContainerBuilder $container): void
    {
        $loader = new AttributeRouteControllerLoader();
        $routes = new RouteCollection();

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if ($definition->isPublic() && !$definition->isAbstract() && $class !== null && class_exists($class)) {
                $routes->addCollection($loader->load($class));
            }
        }

        foreach ($routes as $name => $route) {
            if ($this->matchesShopEntryPoint($route)) {
                throw new ReservedRoutePathException(
                    sprintf('Route "%s" with path "%s" matches the shop entry point', $name, $route->getPath())
                );
            }
        }

        $compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();

        $container->setParameter('oxid.routes', $compiledRoutes);
    }

    private function matchesShopEntryPoint(Route $route): bool
    {
        return $route->getPath() === '/index.php' || preg_match($route->compile()->getRegex(), '/') === 1;
    }
}
