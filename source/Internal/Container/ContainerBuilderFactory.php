<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

/**
 * @internal
 */
class ContainerBuilderFactory
{
    /**
     * @return ContainerBuilder
     */
    public function create(): ContainerBuilder
    {
        $bootstrapContainer = BootstrapContainerFactory::getBootstrapContainer();

        return new ContainerBuilder(
            $bootstrapContainer->get(BasicContextInterface::class)
        );
    }
}
