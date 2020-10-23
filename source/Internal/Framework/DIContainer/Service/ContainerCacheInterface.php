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
    public function put(ContainerBuilder $container): void;

    public function get(): ContainerInterface;

    public function exists(): bool;

    public function invalidate(): void;
}
