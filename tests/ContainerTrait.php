<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use Symfony\Component\DependencyInjection\Container;

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
            $this->container = (new TestContainerFactory())->create();
            $this->container->compile();
            $this->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();
        }
    }
}
