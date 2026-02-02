<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Api;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;

/**
 * Resolves @BundleName/path notation to absolute filesystem paths using
 * the kernel.bundles_metadata registry.
 */
class BundleAwareFileLocator extends FileLocator
{
    public function __construct(private readonly array $bundlesMetadata, array $paths = [])
    {
        parent::__construct($paths);
    }

    public function locate(string $name, ?string $currentPath = null, bool $first = true): string|array
    {
        if (isset($name[0]) && '@' === $name[0]) {
            return $this->locateBundleResource($name);
        }

        return parent::locate($name, $currentPath, $first);
    }

    private function locateBundleResource(string $name): string
    {
        $pos = strpos($name, '/');
        $bundleName = substr($name, 1, $pos !== false ? $pos - 1 : strlen($name) - 1);
        $resourcePath = $pos !== false ? substr($name, $pos) : '';

        if (!isset($this->bundlesMetadata[$bundleName])) {
            throw new FileLocatorFileNotFoundException(
                sprintf('Bundle "%s" does not exist or is not registered.', $bundleName)
            );
        }

        $file = $this->bundlesMetadata[$bundleName]['path'] . $resourcePath;

        if (!file_exists($file)) {
            throw new FileLocatorFileNotFoundException(
                sprintf('File "%s" not found (resolved from "%s").', $file, $name)
            );
        }

        return $file;
    }
}
