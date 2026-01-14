<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaViewServiceInterface
{
    public function getByRole(Id $productId, ProductMediaRole $role): ProductMediaView;

    /** @return array<string, ProductMediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array;

    public function getByPosition(Id $productId, int $position): ProductMediaView;
}
