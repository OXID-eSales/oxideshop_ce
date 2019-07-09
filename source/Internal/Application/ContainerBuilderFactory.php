<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use OxidEsales\EshopCommunity\Internal\Application\BootstrapContainer\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;

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
