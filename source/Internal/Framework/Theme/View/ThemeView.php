<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;

readonly class ThemeView
{
    public function __construct(
        private ThemeMetaData $metaData,
        private bool $active,
    ) {
    }

    public function getId(): string
    {
        return $this->metaData->getId();
    }

    public function getTitle(): string
    {
        return $this->metaData->getTitle();
    }

    public function getDescription(): string
    {
        return $this->metaData->getDescription();
    }

    public function getThumbnail(): string
    {
        return $this->metaData->getThumbnail();
    }

    public function getAuthor(): string
    {
        return $this->metaData->getAuthor();
    }

    public function getVersion(): string
    {
        return $this->metaData->getVersion();
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function hasParentTheme(): bool
    {
        return $this->metaData->getParentTheme() !== '';
    }
}
