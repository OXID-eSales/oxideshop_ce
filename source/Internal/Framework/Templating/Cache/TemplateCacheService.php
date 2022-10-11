<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache;

use FilesystemIterator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;

class TemplateCacheService implements TemplateCacheServiceInterface
{
    public function __construct(
        private BasicContextInterface $basicContext,
        private Filesystem $filesystem
    ) {
    }

    public function invalidateTemplateCache(): void
    {
        $templateCacheDirectory = $this->basicContext->getTemplateCacheDirectory();

        if ($this->filesystem->exists($templateCacheDirectory)) {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($templateCacheDirectory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            $this->filesystem->remove($recursiveIterator);
        }
    }
}
