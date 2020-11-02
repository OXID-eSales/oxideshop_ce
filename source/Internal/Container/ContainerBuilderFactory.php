<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class ContainerBuilderFactory
{
    public function create(): ContainerBuilder
    {
        $bootstrapContainer = BootstrapContainerFactory::getBootstrapContainer();

        return new ContainerBuilder(
            $bootstrapContainer->get(BasicContextInterface::class)
        );
    }
}
