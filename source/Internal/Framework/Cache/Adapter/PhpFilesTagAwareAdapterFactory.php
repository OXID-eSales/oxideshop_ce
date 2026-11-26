<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

readonly class PhpFilesTagAwareAdapterFactory implements TagAwareAdapterFactoryInterface
{
    public function __construct(private CachePoolGenerationInterface $cachePoolGeneration)
    {
    }

    public function create(int $shopId): TagAwareAdapterInterface
    {
        return new GenerationalTagAwareAdapter($this->cachePoolGeneration, $shopId);
    }
}
