<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface AltCopyServiceInterface
{
    public function copyForProduct(Id $productId, int $ownerShopId, int $targetShopId): void;

    public function removeForProduct(Id $productId, int $targetShopId): void;
}
