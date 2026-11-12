<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Exception\AdminUiDirectoryNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Provider\AdminUiDirectoryProviderInterface;
use Symfony\Component\Filesystem\Filesystem;

readonly class AdminUiDirectoryResolver implements AdminUiDirectoryResolverInterface
{
    /** @param iterable<AdminUiDirectoryProviderInterface> $directoryProviders */
    public function __construct(
        private iterable $directoryProviders,
        private Filesystem $filesystem,
    ) {
    }

    /** @return string[] */
    public function getDirectories(): array
    {
        $directories = [];
        foreach ($this->directoryProviders as $directoryProvider) {
            $directories[] = $this->getExistingDirectory($directoryProvider);
        }

        return $directories;
    }

    private function getExistingDirectory(AdminUiDirectoryProviderInterface $directoryProvider): string
    {
        $directory = $directoryProvider->getDirectory();
        if (!$this->filesystem->exists($directory)) {
            throw new AdminUiDirectoryNotFoundException(sprintf('Admin UI directory "%s" does not exist', $directory));
        }

        return $directory;
    }
}
