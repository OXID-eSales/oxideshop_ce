<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;

interface ProductMediaDaoInterface
{
    public function create(string $productId, Media $media, int $position, bool $active): ProductMedia;

    public function update(string $id, int $position, bool $active): void;

    public function delete(string $id): void;

    public function get(string $id): ProductMedia;

    public function getActiveProductMediaList(string $productId): ArrayCollection;

    public function getAllProductMediaList(string $productId): ArrayCollection;
}
