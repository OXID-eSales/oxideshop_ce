<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

interface ProductMediaPathResolverInterface
{
    public function getRelativePath(string $filename): string;
}
