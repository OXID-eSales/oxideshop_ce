<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Filesystem\Path;

class PhpFilesTagAwareAdapterFactory implements TagAwareAdapterFactoryInterface
{
    public function __construct(private readonly ContextInterface $context)
    {
    }

    public function create(int $shopId): TagAwareAdapterInterface
    {
        return new TagAwareAdapter(
            new PhpFilesAdapter(
                namespace: "cache_items_shop_$shopId",
                directory: Path::join($this->context->getCacheDirectory(), 'pool')
            )
        );
    }
}
