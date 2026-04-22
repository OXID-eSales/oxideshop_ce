<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures;

use OxidEsales\EshopCommunity\Internal\Framework\OxidKernel;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\TestBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends OxidKernel
{
    /** @return iterable<BundleInterface> */
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();
        yield new TestBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        parent::configureContainer($container);

        $container->extension('twig', [
            'default_path' => __DIR__ . '/TestBundle/Resources/views',
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        parent::configureRoutes($routes);

        $routes->import(
            __DIR__ . '/TestBundle/Controller/TestApiController.php',
            'attribute'
        );
    }
}
