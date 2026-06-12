<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject;

readonly class ProductMediaImageSizes
{
    public function __construct(
        private string $detailSize,
        private string $iconSize,
        private string $zoomSize,
        private string $thumbnailSize,
    ) {
    }

    public function getDetailSize(): string
    {
        return $this->detailSize;
    }

    public function getIconSize(): string
    {
        return $this->iconSize;
    }

    public function getZoomSize(): string
    {
        return $this->zoomSize;
    }

    public function getThumbnailSize(): string
    {
        return $this->thumbnailSize;
    }
}
