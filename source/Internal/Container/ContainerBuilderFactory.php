<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;

class ContainerBuilderFactory
{
    /**
     * @return ContainerBuilder
     */
    public function create(): ContainerBuilder
    {
        $bootstrapContainer = BootstrapContainerFactory::getBootstrapContainer();
        $shopStateService = $bootstrapContainer->get(ShopStateServiceInterface::class);

        $context = $shopStateService->isLaunched() ? new Context() : $bootstrapContainer->get(BasicContextInterface::class);

        return new ContainerBuilder($context);
    }
}
