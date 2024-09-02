<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use UnitEnum;

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

    private function setParameter(string $name, array|bool|string|int|float|UnitEnum|null $value): void
    {
        if (!$this->container) {
            $this->createContainer();
        }
        $this->container->setParameter($name, $value);
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

    /**
     * Run tests in a separate process if you use this function.
     */
    private function attachContainerToContainerFactory(): void
    {
        if (!$this->container->isCompiled()) {
            $this->container->compile();
        }
        $reflectionClass = new ReflectionClass(ContainerFactory::getInstance());
        $reflectionProperty = $reflectionClass->getProperty('symfonyContainer');
        $reflectionProperty->setValue(ContainerFactory::getInstance(), $this->container);
    }
}
