<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class PhpFilesTagAwareAdapterFactory implements TagAwareAdapterFactoryInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private Filesystem $filesystem,
    ) {
    }

    public function create(int $shopId): TagAwareAdapterInterface
    {
        return new TagAwareAdapter(
            new PhpFilesAdapter(
                namespace: "cache_items_shop_{$shopId}_{$this->getGeneration($shopId)}",
                directory: Path::join($this->context->getCacheDirectory(), 'pool')
            )
        );
    }

    private function getGeneration(int $shopId): string
    {
        $generationFilePath = $this->context->getCachePoolGenerationFilePath($shopId);

        return $this->filesystem->exists($generationFilePath) ? $this->filesystem->readFile($generationFilePath) : '0';
    }
}
