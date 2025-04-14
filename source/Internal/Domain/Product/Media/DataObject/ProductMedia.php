<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

class ProductMedia
{
    private ?int $position = null;
    private bool $active = true;

    public function __construct(
        private readonly Id $id,
        private readonly Id $productId,
        private readonly Media $media,
        private ProductMediaRoleSet $roleSet,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProductId(): Id
    {
        return $this->productId;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function hasPosition(): bool
    {
        return $this->position !== null;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getRoleSet(): ProductMediaRoleSet
    {
        return $this->roleSet;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }
}
