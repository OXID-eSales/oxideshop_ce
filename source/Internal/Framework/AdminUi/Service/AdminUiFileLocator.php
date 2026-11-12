<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class AdminUiFileLocator implements AdminUiFileLocatorInterface
{
    public function __construct(
        private AdminUiDirectoryResolverInterface $directoryResolver,
        private Filesystem $filesystem,
    ) {
    }

    /** @return string[] */
    public function locate(string $relativePath): array
    {
        foreach ($this->directoryResolver->getDirectories() as $directory) {
            $path = Path::join($directory, $relativePath);
            if ($this->filesystem->exists($path)) {
                return [$path];
            }
        }

        return [];
    }
}
