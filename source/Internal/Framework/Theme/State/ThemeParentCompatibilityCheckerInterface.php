<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ParentVersionUnspecifiedException;

interface ThemeParentCompatibilityCheckerInterface
{
    /**
     * @throws ParentThemeNotInstalledException
     * @throws ParentVersionUnspecifiedException
     * @throws ParentVersionsNotDeclaredException
     * @throws ParentVersionMismatchException
     */
    public function assertCompatible(string $themeId, int $shopId): void;
}
