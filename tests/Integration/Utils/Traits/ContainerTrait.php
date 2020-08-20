<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Utils\Traits;

use OxidEsales\EshopCommunity\Tests\Integration\Utils\TestContainerFactory;

/**
 * Trait ContainerTrait
 * @package OxidEsales\EshopCommunity\Tests\Integration\Utils\Traits
 */
trait ContainerTrait
{
    private $serviceMocks = [];

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
