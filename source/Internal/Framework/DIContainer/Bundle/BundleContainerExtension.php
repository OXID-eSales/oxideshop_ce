<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

readonly class BundleContainerExtension implements BundleContainerExtensionInterface
{
    /**
     * @param BundleInterface[] $bundles
     */
    public function initializeBundles(SymfonyContainerBuilder $container, array $bundles): void
    {
        $this->registerBundleExtensions($container, $bundles);
        $this->buildBundles($container, $bundles);
    }

    /**
     * @param BundleInterface[] $bundles
     */
    public function loadExtensionConfigs(SymfonyContainerBuilder $container, array $bundles): void
    {
        $this->prependExtensionConfigs($container, $bundles);

        foreach ($bundles as $bundle) {
            $extension = $bundle->getContainerExtension();
            if ($extension !== null && $container->hasExtension($extension->getAlias())) {
                $container->loadFromExtension($extension->getAlias());
            }
        }
    }

    private function registerBundleExtensions(SymfonyContainerBuilder $container, array $bundles): void
    {
        foreach ($bundles as $bundle) {
            $extension = $bundle->getContainerExtension();
            if ($extension !== null) {
                $container->registerExtension($extension);
            }
        }
    }

    private function buildBundles(SymfonyContainerBuilder $container, array $bundles): void
    {
        foreach ($bundles as $bundle) {
            $bundle->build($container);
        }
    }

    private function prependExtensionConfigs(SymfonyContainerBuilder $container, array $bundles): void
    {
        foreach ($bundles as $bundle) {
            $extension = $bundle->getContainerExtension();
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($container);
            }
        }
    }
}
