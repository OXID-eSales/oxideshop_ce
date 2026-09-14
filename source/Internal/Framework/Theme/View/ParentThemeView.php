<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View;

readonly class ParentThemeView
{
    /** @param string[] $versions */
    public function __construct(
        private string $id,
        private string $title,
        private array $versions,
        private bool $compatible,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return string[] */
    public function getVersions(): array
    {
        return $this->versions;
    }

    public function isCompatible(): bool
    {
        return $this->compatible;
    }
}
