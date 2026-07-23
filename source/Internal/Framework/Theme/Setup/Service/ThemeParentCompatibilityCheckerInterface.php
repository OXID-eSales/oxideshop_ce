<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\Exception\ThemeInheritanceCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeMetadataInvalidException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionUnspecifiedException;

interface ThemeParentCompatibilityCheckerInterface
{
    /**
     * @throws ThemeInheritanceCycleException
     * @throws ParentThemeNotInstalledException
     * @throws ParentThemeMetadataInvalidException
     * @throws ParentVersionUnspecifiedException
     * @throws ParentVersionsNotDeclaredException
     * @throws ParentVersionMismatchException
     */
    public function assertCompatible(string $themeId, int $shopId): void;
}
