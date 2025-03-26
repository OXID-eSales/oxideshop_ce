<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;

final readonly class ProductMedia
{
    public function __construct(
        private string $id,
        private string $productId,
        private Media $media,
        private int $position,
        private bool $active = true
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
