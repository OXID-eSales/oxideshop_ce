<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\DataObject\ParentThemeView;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\DataObject\ThemeView;

interface ThemeViewServiceInterface
{
    /** @throws ThemeNotLoadableException */
    public function getTheme(string $themeId, int $shopId): ThemeView;

    /** @throws ThemeNotLoadableException */
    public function getParentTheme(string $themeId, int $shopId): ParentThemeView;

    /**
     * @return ThemeView[]
     */
    public function getThemes(int $shopId): array;
}
