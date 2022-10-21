<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerCacheInterface
{
    public function put(ContainerBuilder $container, int $shopId): void;

    public function get(int $shopId): ContainerInterface;

    public function exists(int $shopId): bool;

    public function invalidate(int $shopId): void;
}
