<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;

readonly class ProductMediaView
{
    public function __construct(
        private string $detailUrl,
        private string $iconUrl,
        private string $zoomUrl,
        private string $thumbnailUrl,
        private MediaAttributes $attributes,
        private bool $isFallback = false,
    ) {
    }

    public function getDetailUrl(): string
    {
        return $this->detailUrl;
    }

    public function getIconUrl(): string
    {
        return $this->iconUrl;
    }

    public function getZoomUrl(): string
    {
        return $this->zoomUrl;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function isFallback(): bool
    {
        return $this->isFallback;
    }

    public function getAttributes(): MediaAttributes
    {
        return $this->attributes;
    }

    public function getAlt(string $fallback = ''): string
    {
        if ($this->attributes->has('alt') && $this->attributes->getAlt() !== '') {
            return $this->attributes->getAlt();
        }
        return trim(strip_tags($fallback));
    }
}
