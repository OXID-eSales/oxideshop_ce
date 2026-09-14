<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Definition;

use Symfony\Component\Config\Definition\NodeInterface;

interface MetaDataTreeBuilderFactoryInterface
{
    public function create(): NodeInterface;
}
