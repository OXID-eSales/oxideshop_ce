<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
interface BundleManagerInterface
{
    /**
     * @param BundleInterface[] $bundles
     */
    public function boot(ContainerInterface $container, array $bundles): void;

    public function shutdown(): void;

    public function isBooted(): bool;
}
