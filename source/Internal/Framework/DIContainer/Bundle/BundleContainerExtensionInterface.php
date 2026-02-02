<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @internal
 */
interface BundleContainerExtensionInterface
{
    /**
     * @param BundleInterface[] $bundles
     */
    public function initializeBundles(SymfonyContainerBuilder $container, array $bundles): void;

    /**
     * @param BundleInterface[] $bundles
     */
    public function loadExtensionConfigs(SymfonyContainerBuilder $container, array $bundles): void;
}
