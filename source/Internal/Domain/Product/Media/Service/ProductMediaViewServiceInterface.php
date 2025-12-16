<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaView;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaViewServiceInterface
{
    public function getByRole(Id $productId, ProductMediaRole $role): MediaView;

    /** @return array<string, MediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array;

    public function getByPosition(Id $productId, int $position): MediaView;
}
