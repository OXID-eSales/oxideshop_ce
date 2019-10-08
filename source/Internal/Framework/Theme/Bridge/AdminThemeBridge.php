<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

class AdminThemeBridge implements AdminThemeBridgeInterface
{
    /**
     * @var string
     */
    private $activeThemeName;

    /**
     * AdminThemeBridge constructor.
     *
     * @param string $activeThemeName
     */
    public function __construct(string $activeThemeName)
    {
        $this->activeThemeName = $activeThemeName;
    }

    /**
     * @return string
     */
    public function getActiveTheme(): string
    {
        return $this->activeThemeName;
    }
}
