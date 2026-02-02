<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
class BundleManager implements BundleManagerInterface
{
    private bool $booted = false;

    /** @var BundleInterface[] */
    private array $bundles = [];

    /**
     * @param BundleInterface[] $bundles
     */
    public function boot(ContainerInterface $container, array $bundles): void
    {
        if ($this->booted) {
            return;
        }

        $this->bundles = $bundles;

        foreach ($this->bundles as $bundle) {
            $bundle->setContainer($container);
            $bundle->boot();
        }

        $this->booted = true;
    }

    public function shutdown(): void
    {
        if (!$this->booted) {
            return;
        }

        foreach ($this->bundles as $bundle) {
            $bundle->shutdown();
            $bundle->setContainer(null);
        }

        $this->bundles = [];
        $this->booted = false;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}
