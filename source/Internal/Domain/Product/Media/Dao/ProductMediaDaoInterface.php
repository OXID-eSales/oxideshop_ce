<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaSorting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface ProductMediaDaoInterface
{
    public function add(ProductMedia $productMedia): void;

    /** @throws EntryDoesNotExistDaoException */
    public function get(Id $id): ProductMedia;

    public function getAllProductMedia(Id $productId): ArrayCollection;

    public function update(ProductMedia $productMedia): void;

    public function delete(Id $id): void;

    public function sort(ProductMediaSorting $sorting): void;
}
