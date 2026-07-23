<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;

readonly class ThemeChain
{
    /** @param string[] $themeIds */
    public function __construct(
        private array $themeIds,
    ) {
    }

    /** @return string[] */
    public function getThemeIds(): array
    {
        return $this->themeIds;
    }

    public function hasParentTheme(): bool
    {
        return isset($this->themeIds[1]);
    }

    /** @throws ParentThemeNotFoundException */
    public function getParentThemeId(): string
    {
        return $this->themeIds[1] ?? throw new ParentThemeNotFoundException();
    }
}