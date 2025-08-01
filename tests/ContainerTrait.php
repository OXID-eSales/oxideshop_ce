<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;
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
        $this->createContainer();
        $this->container->setParameter($name, $value);
        $this->compileContainer();
        $this->replaceContainerInstance();
    }

    private function prepareContainer(): void
    {
        if ($this->container === null) {
            $this->container = TestContainerFactory::get();
        }
    }

    private function createContainer(): void
    {
        $this->container = (new TestContainerFactory())->create();
    }

    private function compileContainer(): void
    {
        $this->container->compile(true);
    }

    private function replaceContainerInstance(): void
    {
        if (!$this->container->isCompiled()) {
            $this->container->compile(true);
        }
        TestContainerFactory::setContainer($this->container);
    }

    private function loadYamlFixture(string $fixtureDir): void
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(Path::join($fixtureDir, 'services.yaml'));
    }

    private function replaceService(string $id, object $service): void
    {
        $this->container->set($id, $service);
        $this->container->autowire($id, $id);
    }

    private function rewriteProjectConfiguration(array $config): void
    {
        (new Filesystem())->dumpFile(
            Path::join(
                ContainerFacade::get(BasicContextInterface::class)->getProjectConfigurationDirectory(),
                'parameters.yaml'
            ),
            Yaml::dump($config)
        );
    }
}
