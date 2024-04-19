<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 * @mixin Container
 */
trait ContainerTrait
{
    private $container;

    protected function get(string $serviceId)
    {
        $this->prepareContainer();
        return $this->container->get($serviceId);
    }

    private function getParameter(string $name)
    {
        $this->prepareContainer();
        return $this->container->getParameter($name);
    }

    private function prepareContainer(): void
    {
        if ($this->container === null) {
            $this->createContainer();
            $this->compileContainer();
        }
    }

    private function createContainer(): void
    {
        $this->container = (new TestContainerFactory())->create();
    }

    private function compileContainer(): void
    {
        $this->container->compile(true);
        $this->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();
    }

    private function loadYamlFixture(string $fixtureDir): void
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(Path::join($fixtureDir, 'services.yaml'));
    }

    private function replaceService(string $id, ?object $service): void
    {
        $this->container->set($id, $service);
        $this->container->autowire($id, $id);
    }
}
