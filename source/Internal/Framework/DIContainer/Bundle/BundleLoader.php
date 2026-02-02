<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
readonly class BundleLoader implements BundleLoaderInterface
{
    public function __construct(private BasicContextInterface $context)
    {
    }

    public function loadBundles(?string $environment = null): array
    {
        $configPath = $this->context->getBundlesConfigFilePath();

        if (!file_exists($configPath)) {
            return [];
        }

        $config = Yaml::parseFile($configPath);

        if (!isset($config['bundles']) || !is_array($config['bundles'])) {
            return [];
        }

        $bundles = [];

        foreach ($config['bundles'] as $bundleClass => $options) {
            if ($this->shouldLoadBundle($options, $environment)) {
                $bundles[] = $this->instantiateBundle($bundleClass);
            }
        }

        return $bundles;
    }

    private function shouldLoadBundle(array $envs, ?string $environment): bool
    {
        if ($environment === null) {
            return true;
        }

        return (bool) ($envs[$environment] ?? $envs['all'] ?? false);
    }

    /**
     * @throws \RuntimeException
     */
    private function instantiateBundle(string $bundleClass): BundleInterface
    {
        if (!$this->classExists($bundleClass)) {
            throw new \RuntimeException(sprintf(
                'Bundle class "%s" does not exist.',
                $bundleClass
            ));
        }

        $bundle = new $bundleClass();

        if (!$bundle instanceof BundleInterface) {
            throw new \RuntimeException(sprintf(
                'Bundle class "%s" must implement %s.',
                $bundleClass,
                BundleInterface::class
            ));
        }

        return $bundle;
    }

    private function classExists(string $class): bool
    {
        try {
            return class_exists($class);
        } catch (\Throwable) {
            return false;
        }
    }
}
