<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ParentThemeNotFoundException;

readonly class ThemeParentProvider implements ThemeParentProviderInterface
{
    public function __construct(
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function hasParentTheme(string $themeId, int $shopId): bool
    {
        return $this->getParentTheme($themeId, $shopId) !== '';
    }

    public function getParentThemeId(string $themeId, int $shopId): string
    {
        $parentThemeId = $this->getParentTheme($themeId, $shopId);
        if ($parentThemeId === '') {
            throw new ParentThemeNotFoundException();
        }

        return $parentThemeId;
    }

    private function getParentTheme(string $themeId, int $shopId): string
    {
        return $this->themeMetaDataByIdProvider->get($themeId, $shopId)->getParentTheme();
    }
}
