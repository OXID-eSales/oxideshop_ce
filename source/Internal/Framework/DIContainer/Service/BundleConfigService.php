<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Service for managing bundle configuration (bundles.yaml).
 *
 * Configuration format:
 * bundles:
 *     Vendor\SomeBundle\VendorSomeBundle: { all: true }
 *     Symfony\Bundle\DebugBundle\DebugBundle: { environments: ['dev'] }
 *
 * @internal
 */
readonly class BundleConfigService implements BundleConfigServiceInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private Filesystem $filesystem
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getBundles(): array
    {
        $config = $this->loadConfig();
        return $config['bundles'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function addBundle(string $bundleClass, array $options = ['all' => true]): void
    {
        $config = $this->loadConfig();

        if (!isset($config['bundles'])) {
            $config['bundles'] = [];
        }

        $config['bundles'][$bundleClass] = $options;

        $this->saveConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function removeBundle(string $bundleClass): void
    {
        $config = $this->loadConfig();

        if (isset($config['bundles'][$bundleClass])) {
            unset($config['bundles'][$bundleClass]);
            $this->saveConfig($config);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasBundle(string $bundleClass): bool
    {
        $config = $this->loadConfig();
        return isset($config['bundles'][$bundleClass]);
    }

    /**
     * @inheritDoc
     */
    public function updateBundle(string $bundleClass, array $options): void
    {
        if (!$this->hasBundle($bundleClass)) {
            throw new \RuntimeException(sprintf(
                'Bundle "%s" is not configured.',
                $bundleClass
            ));
        }

        $config = $this->loadConfig();
        $config['bundles'][$bundleClass] = $options;
        $this->saveConfig($config);
    }

    /**
     * Load configuration from bundles.yaml.
     */
    private function loadConfig(): array
    {
        $configPath = $this->context->getBundlesConfigFilePath();

        if (!file_exists($configPath)) {
            return ['bundles' => []];
        }

        return Yaml::parseFile($configPath) ?? ['bundles' => []];
    }

    /**
     * Save configuration to bundles.yaml.
     */
    private function saveConfig(array $config): void
    {
        $configPath = $this->context->getBundlesConfigFilePath();
        $configDir = dirname($configPath);

        if (!$this->filesystem->exists($configDir)) {
            $this->filesystem->mkdir($configDir);
        }

        $this->filesystem->dumpFile($configPath, Yaml::dump($config, 3, 2));
    }
}
