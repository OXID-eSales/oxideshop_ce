<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;

interface ProductMediaPathResolverInterface
{
    public function resolve(string $productId, string $filename): MediaPath;
}
