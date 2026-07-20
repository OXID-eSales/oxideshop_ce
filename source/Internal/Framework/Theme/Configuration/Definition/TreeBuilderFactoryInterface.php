<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Definition;

use Symfony\Component\Config\Definition\NodeInterface;

interface TreeBuilderFactoryInterface
{
    public function create(): NodeInterface;
}
