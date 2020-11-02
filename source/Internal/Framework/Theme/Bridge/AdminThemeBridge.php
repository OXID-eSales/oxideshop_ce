<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

class AdminThemeBridge implements AdminThemeBridgeInterface
{
    /**
     * @var string
     */
    private $activeThemeName;

    /**
     * AdminThemeBridge constructor.
     */
    public function __construct(string $activeThemeName)
    {
        $this->activeThemeName = $activeThemeName;
    }

    public function getActiveTheme(): string
    {
        return $this->activeThemeName;
    }
}
