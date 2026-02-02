<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

/**
 * Service for managing bundle configuration (bundles.yaml).
 *
 * @internal
 */
interface BundleConfigServiceInterface
{
    /**
     * Get all configured bundles.
     *
     * @return array<string, array> Bundle class names as keys, options as values
     */
    public function getBundles(): array;

    /**
     * Add a bundle to configuration.
     *
     * @param string $bundleClass Fully qualified bundle class name
     * @param array $options Bundle options (e.g., ['all' => true] or ['environments' => ['dev']])
     */
    public function addBundle(string $bundleClass, array $options = ['all' => true]): void;

    /**
     * Remove a bundle from configuration.
     *
     * @param string $bundleClass Fully qualified bundle class name
     */
    public function removeBundle(string $bundleClass): void;

    /**
     * Check if a bundle is configured.
     *
     * @param string $bundleClass Fully qualified bundle class name
     */
    public function hasBundle(string $bundleClass): bool;

    /**
     * Update bundle options.
     *
     * @param string $bundleClass Fully qualified bundle class name
     * @param array $options New bundle options
     */
    public function updateBundle(string $bundleClass, array $options): void;
}
