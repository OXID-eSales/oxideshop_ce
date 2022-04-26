<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use ArrayIterator;
use IteratorAggregate;

class ModuleIdChain implements IteratorAggregate
{
    public function __construct(private array $moduleIds)
    {
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->moduleIds);
    }
}
