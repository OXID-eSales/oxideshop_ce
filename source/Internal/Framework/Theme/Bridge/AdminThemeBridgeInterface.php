<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

/**
 * @internal
 */
interface AdminThemeBridgeInterface
{
    /**
     * @return string
     */
    public function getActiveTheme(): string;
}
