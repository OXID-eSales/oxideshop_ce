<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
interface BundleLoaderInterface
{
    /**
     * Load and instantiate bundles from configuration.
     *
     * @param string|null $environment Optional environment filter (e.g., 'dev', 'prod')
     * @return BundleInterface[]
     */
    public function loadBundles(?string $environment = null): array;
}
