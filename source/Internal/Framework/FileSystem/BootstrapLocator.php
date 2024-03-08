<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use RuntimeException;
use Symfony\Component\Filesystem\Path;

use function dirname;
use function is_dir;

class BootstrapLocator
{
    private string $projectRoot = '';

    public function getProjectRoot(): string
    {
        if (!$this->projectRoot) {
            $path = __DIR__;
            while (!is_dir(Path::join($path, 'vendor'))) {
                if ($this->isFilesystemRootDir($path)) {
                    throw new RuntimeException('Can not determine project root directory!');
                }
                $path = $this->getParentDir($path);
            }
            $this->projectRoot = $path;
        }
        return $this->projectRoot;
    }

    private function isFilesystemRootDir(string $path): bool
    {
        return $path === $this->getParentDir($path);
    }

    private function getParentDir(string $path): string
    {
        return dirname($path);
    }
}
