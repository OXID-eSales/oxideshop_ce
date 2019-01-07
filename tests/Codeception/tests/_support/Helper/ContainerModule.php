<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContainerModule extends \Codeception\Module
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function _initialize()
    {
        parent::_initialize();

        $this->container = $this->getContainer();
    }

    public function grabService($service)
    {
        if (!$this->container->has($service)) {
            $this->fail("Service $service is not available in container");
        }
        return $this->container->get($service);
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer() : ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../'));
        $loader->load('services.yaml');
        return $containerBuilder;
    }

}
