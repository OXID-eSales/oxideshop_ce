<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\ParentInfo;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ThemeParentNotFoundException;

readonly class ThemeParentInfo
{
    public function __construct(
        private ThemeInheritance $inheritance,
        private ?string $parentThemeTitle,
        private array $parentThemeVersions,
        private bool $hasActivationError,
        private bool $hasResolutionError = false,
    ) {
    }

    public function hasResolutionError(): bool
    {
        return $this->hasResolutionError;
    }

    public function hasParentTheme(): bool
    {
        return $this->inheritance->hasParentTheme();
    }

    /** @throws ThemeParentNotFoundException */
    public function getParentThemeId(): string
    {
        return $this->inheritance->getParentThemeId();
    }

    public function getParentThemeTitle(): ?string
    {
        return $this->parentThemeTitle;
    }

    /** @return string[] */
    public function getParentThemeVersions(): array
    {
        return $this->parentThemeVersions;
    }

    public function hasActivationError(): bool
    {
        return $this->hasActivationError;
    }
}
