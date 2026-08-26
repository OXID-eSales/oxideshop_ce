<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;

class ThemeMetaDataByIdProvider implements ThemeMetaDataByIdProviderInterface
{
    /** @var array<string, ThemeMetaData> */
    private array $cache = [];

    public function __construct(
        private readonly ThemePathResolverInterface $themePathResolver,
        private readonly ThemeMetaDataProviderInterface $themeMetaDataProvider,
    ) {
    }

    public function getById(string $themeId, int $shopId): ThemeMetaData
    {
        return $this->cache["$themeId:$shopId"] ??= $this->resolve($themeId, $shopId);
    }

    private function resolve(string $themeId, int $shopId): ThemeMetaData
    {
        $themePath = $this->themePathResolver->getFullThemePathFromConfiguration($themeId, $shopId);
        $themeMetaData = $this->themeMetaDataProvider->get($themePath);

        if ($themeMetaData->getId() !== $themeId) {
            throw new InvalidThemeMetaDataException(
                "Theme '$themeId' is configured with a source whose metadata.yaml declares id '{$themeMetaData->getId()}'"
            );
        }

        return $themeMetaData;
    }
}
