<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

interface TagAwareAdapterFactoryInterface
{
    public function create(int $shopId): TagAwareAdapterInterface;
}
