<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ContainerCache
{
    public function put(ContainerBuilder $container): void;

    public function get(): \ProjectServiceContainer;

    public function exists(): bool;

    public function invalidate(): void;
}
