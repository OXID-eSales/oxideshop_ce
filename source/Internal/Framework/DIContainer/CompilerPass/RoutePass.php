<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass;

use OxidEsales\EshopCommunity\Internal\Framework\Api\AttributeRouteControllerLoader;
use OxidEsales\EshopCommunity\Internal\Framework\Api\BundleAwareFileLocator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection;

class RoutePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $routes = new RouteCollection();

        $this->loadAttributeRoutes($container, $routes);
        $this->loadBundleDefaultRoutes($container, $routes);
        $this->loadAppRoutes($container, $routes);

        $container->setParameter('oxid.routes', (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes());
    }

    private function loadAttributeRoutes(ContainerBuilder $container, RouteCollection $routes): void
    {
        $loader = new AttributeRouteControllerLoader();

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if ($definition->isPublic() && !$definition->isAbstract() && $class !== null && class_exists($class)) {
                $routes->addCollection($loader->load($class));
            }
        }
    }

    private function loadBundleDefaultRoutes(ContainerBuilder $container, RouteCollection $routes): void
    {
        if (!$container->hasParameter('kernel.bundles_metadata')) {
            return;
        }

        $loader = new YamlFileLoader(new FileLocator());

        foreach ($container->getParameter('kernel.bundles_metadata') as $metadata) {
            $routesFile = $metadata['path'] . '/Resources/config/routes.yaml';
            if (file_exists($routesFile)) {
                $routes->addCollection($loader->load($routesFile));
            }
        }
    }

    private function loadAppRoutes(ContainerBuilder $container, RouteCollection $routes): void
    {
        $routesFile = $container->getParameter('kernel.project_dir') . '/var/configuration/routes.yaml';
        if (!file_exists($routesFile)) {
            return;
        }

        $bundlesMetadata = $container->hasParameter('kernel.bundles_metadata')
            ? $container->getParameter('kernel.bundles_metadata')
            : [];

        $locator = new BundleAwareFileLocator($bundlesMetadata);
        $yamlLoader = new YamlFileLoader($locator);
        $xmlLoader = new XmlFileLoader($locator);
        $resolver = new LoaderResolver([$yamlLoader, $xmlLoader]);

        try {
            $routes->addCollection($yamlLoader->load($routesFile));
        } catch (LoaderLoadException $e) {
            if (!$e->getPrevious() instanceof FileLocatorFileNotFoundException) {
                throw $e;
            }
        }
    }
}
