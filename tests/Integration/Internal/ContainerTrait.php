<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

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
        if ($this->container === null) {
            $this->container = (new TestContainerFactory())->create();
            $this->container->compile();
            $this->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();
        }

        return $this->container->get($serviceId);
    }
}
