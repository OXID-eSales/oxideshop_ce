<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Definition;

use Symfony\Component\Config\Definition\NodeInterface;

interface TreeBuilderFactoryInterface
{
    public function create(): NodeInterface;
}
