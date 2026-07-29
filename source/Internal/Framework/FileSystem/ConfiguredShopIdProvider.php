<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use DirectoryIterator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ConfiguredShopIdProvider implements ConfiguredShopIdProviderInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private Filesystem $filesystem,
    ) {
    }

    public function getShopIds(): array
    {
        $shopIds = [];
        $directory = $this->getShopsConfigurationDirectory();

        if ($this->filesystem->exists($directory)) {
            foreach (new DirectoryIterator($directory) as $fileInfo) {
                if ($fileInfo->isDir() && is_numeric($fileInfo->getFilename())) {
                    $shopIds[] = (int)$fileInfo->getFilename();
                }
            }
        }

        sort($shopIds);

        return $shopIds;
    }

    private function getShopsConfigurationDirectory(): string
    {
        return Path::getDirectory(
            $this->context->getShopConfigurationDirectory($this->context->getDefaultShopId())
        );
    }
}
