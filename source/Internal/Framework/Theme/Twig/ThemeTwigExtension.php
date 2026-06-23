<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class ThemeTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly ThemeViewProxy $themeViewProxy)
    {
    }

    public function getGlobals(): array
    {
        return ['theme' => $this->themeViewProxy];
    }
}
