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
use UnitEnum;

/**
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

    private function setParameter(string $name, array|bool|string|int|float|UnitEnum|null $value): void
    {
        $container = (new TestContainerFactory())->create();
        $container->setParameter($name, $value);
        $container->compile(true);

        TestContainerFactory::setContainer($container);
        $this->container = $container;
    }

    private function prepareContainer(): void
    {
        if ($this->container === null) {
            $this->container = TestContainerFactory::get();
        }
    }

    private function loadYamlFixture(string $fixtureDir): void
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(Path::join($fixtureDir, 'services.yaml'));
    }
}
