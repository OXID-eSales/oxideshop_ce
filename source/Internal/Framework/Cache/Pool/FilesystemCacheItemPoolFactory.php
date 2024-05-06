<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Pool;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Path;

class FilesystemCacheItemPoolFactory implements CacheItemPoolFactoryInterface
{
    public function __construct(private readonly ContextInterface $context)
    {
    }

    public function create(int $shopId): CacheItemPoolInterface
    {
        return new FilesystemAdapter(
            namespace: "cache_items_shop_$shopId",
            directory: Path::join($this->context->getCacheDirectory(), 'pool',)
        );
    }
}
