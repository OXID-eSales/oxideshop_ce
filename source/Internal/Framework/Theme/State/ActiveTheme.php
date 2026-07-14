<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;

readonly class ActiveTheme
{
    public function __construct(
        private ThemeInheritance $inheritance,
    ) {
    }

    public function getId(): string
    {
        return $this->inheritance->getThemeId();
    }

    public function getInheritance(): ThemeInheritance
    {
        return $this->inheritance;
    }
}
