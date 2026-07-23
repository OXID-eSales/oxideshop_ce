<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChain;

readonly class ActiveTheme
{
    public function __construct(
        private ThemeChain $chain,
    ) {
    }

    public function getId(): string
    {
        return $this->chain->getThemeIds()[0];
    }

    public function getChain(): ThemeChain
    {
        return $this->chain;
    }
}
