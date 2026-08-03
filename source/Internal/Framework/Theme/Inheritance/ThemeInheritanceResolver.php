<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ThemeParentNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;

readonly class ThemeInheritanceResolver implements ThemeInheritanceResolverInterface
{
    public function __construct(
        private ThemeParentProviderInterface $themeParentProvider,
    ) {
    }

    public function resolve(string $themeId, int $shopId): ThemeInheritance
    {
        try {
            $parentThemeId = $this->themeParentProvider->getParentThemeId($themeId, $shopId);
        } catch (ThemeParentNotFoundException) {
            return new ThemeInheritance($themeId, null);
        }

        if ($parentThemeId === $themeId) {
            throw new ThemeInheritanceCycleException(
                "Theme '$themeId' declares itself as its own parent theme"
            );
        }

        if ($this->themeParentProvider->hasParentTheme($parentThemeId, $shopId)) {
            throw new ThemeInheritanceDepthExceededException(
                "Theme '$themeId' declares '$parentThemeId' as its parent, but '$parentThemeId' is itself "
                . 'a child theme; only one level of theme inheritance is supported'
            );
        }

        return new ThemeInheritance($themeId, $parentThemeId);
    }
}
