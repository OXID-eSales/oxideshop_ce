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

class ProjectRootLocator
{
    private static ?string $projectRoot = null;

    public function getProjectRoot(): string
    {
        return self::$projectRoot ??= $this->locateProjectRoot();
    }

    private function locateProjectRoot(): string
    {
        $path = __DIR__;
        while (!is_dir(Path::join($path, 'vendor'))) {
            if ($this->isFilesystemRootDir($path)) {
                throw new RuntimeException('Can not determine project root directory!');
            }
            $path = $this->getParentDir($path);
        }

        return $path;
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
