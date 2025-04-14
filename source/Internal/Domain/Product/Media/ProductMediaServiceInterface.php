<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaServiceInterface
{
    public function add(ProductMedia $productMedia): void;

    public function get(Id $mediaId): ProductMedia;

    public function remove(Id $mediaId): void;

    public function addMediaRole(ProductMedia $productMedia, ProductMediaRole $role): void;

    public function removeMediaRole(ProductMedia $productMedia, ProductMediaRole $role): void;

    public function sort(array $idsSorted): void;

    public function activate(ProductMedia $productMedia): void;

    public function deactivate(ProductMedia $productMedia): void;
}
