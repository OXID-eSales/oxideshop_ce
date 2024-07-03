<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace  OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Fixtures\Pe\Internal;

use  OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Fixtures\Ce\Internal\ServiceInterface;

class ServiceDecorator implements ServiceInterface
{
    public function __construct(
        private readonly ServiceInterface $decorated,
        private readonly string $namespace
    ) {
    }

    public function getNamespace(): string
    {
        return "{$this->decorated->getNamespace()}.$this->namespace";
    }
}
