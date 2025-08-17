<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject;

readonly class MediaView
{
    public function __construct(
        private string $url,
        private string $iconUrl,
        private string $zoomUrl,
        private string $thumbnailUrl,
        private bool $isFallback = false
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
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
}
