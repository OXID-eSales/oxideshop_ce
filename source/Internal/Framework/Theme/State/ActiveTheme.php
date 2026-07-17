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
        private bool $isChildTheme,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isChildTheme(): bool
    {
        return $this->isChildTheme;
    }
}
