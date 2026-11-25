<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

readonly class PhpFilesTagAwareAdapterFactory implements TagAwareAdapterFactoryInterface
{
    public function __construct(private CachePoolGenerationInterface $cachePoolGeneration)
    {
    }

    public function create(int $shopId): TagAwareAdapterInterface
    {
        $directory = $this->cachePoolGeneration->getDirectory($shopId);

        return new TagAwareAdapter(
            new PhpFilesAdapter(namespace: 'items', directory: $directory),
            new FilesystemAdapter(namespace: 'tags', directory: $directory)
        );
    }
}
