<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

readonly class ActiveTheme
{
    public function __construct(
        private string $id,
        private string $parentThemeId = '',
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function hasParentTheme(): bool
    {
        return $this->parentThemeId !== '';
    }

    public function getParentThemeId(): string
    {
        return $this->parentThemeId;
    }
}
