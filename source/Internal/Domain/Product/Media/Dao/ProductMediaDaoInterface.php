<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaDaoInterface
{
    public function add(ProductMedia $productMedia): void;

    public function update(ProductMedia $productMedia): void;

    public function delete(Id $id): void;

    public function sort(ProductMediaSorting $sorting): void;

    public function get(Id $id): ProductMedia;

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAll(Id $productId): ArrayCollection;

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllActive(Id $productId): ArrayCollection;

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): ArrayCollection;

    /** @return ArrayCollection<int, ProductMedia> */
    public function getAllActiveByRole(Id $productId, ProductMediaRole $role): ArrayCollection;

    public function getByRole(Id $productId, ProductMediaRole $role): ?ProductMedia;

    public function getActiveByRole(Id $productId, ProductMediaRole $role): ?ProductMedia;

    public function getActiveByPosition(Id $productId, int $position): ?ProductMedia;

    public function getFirstActive(Id $productId): ?ProductMedia;
}
