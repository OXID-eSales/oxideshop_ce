<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductVariantMediaServiceInterface
{
    public function assignFromParentToVariant(Id $parentProductId, Id $variantProductId): void;
}
