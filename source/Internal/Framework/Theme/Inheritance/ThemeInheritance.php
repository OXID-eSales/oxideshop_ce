<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ThemeParentNotFoundException;

readonly class ThemeInheritance
{
    public function __construct(
        private string $themeId,
        private ?string $parentThemeId,
    ) {
    }

    public function getThemeId(): string
    {
        return $this->themeId;
    }

    public function hasParentTheme(): bool
    {
        return $this->parentThemeId !== null;
    }

    /** @throws ThemeParentNotFoundException */
    public function getParentThemeId(): string
    {
        return $this->parentThemeId ?? throw new ThemeParentNotFoundException();
    }
}
