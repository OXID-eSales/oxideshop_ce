<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\Exception\ThemeInheritanceCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;

readonly class ThemeChainResolver implements ThemeChainResolverInterface
{
    public function __construct(
        private ThemeParentProviderInterface $themeParentProvider,
    ) {
    }

    public function getThemeChain(string $themeId, int $shopId): ThemeChain
    {
        if (!$this->themeParentProvider->hasParentTheme($themeId, $shopId)) {
            return new ThemeChain([$themeId]);
        }

        $parentThemeId = $this->themeParentProvider->getParentThemeId($themeId, $shopId);

        if ($parentThemeId === $themeId) {
            throw new ThemeInheritanceCycleException(
                "Theme '$themeId' declares itself as its own parent theme"
            );
        }

        return new ThemeChain([$themeId, $parentThemeId]);
    }
}