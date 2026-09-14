<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;

readonly class ThemeMetaDataByIdProvider implements ThemeMetaDataByIdProviderInterface
{
    public function __construct(
        private ThemePathResolverInterface $themePathResolver,
        private ThemeMetaDataProviderInterface $themeMetaDataProvider,
    ) {
    }

    public function getById(string $themeId, int $shopId): ThemeMetaData
    {
        $themeMetaData = $this->themeMetaDataProvider->get(
            $this->themePathResolver->getAbsolutePath($themeId, $shopId)
        );

        if ($themeMetaData->getId() !== $themeId) {
            throw new InvalidThemeMetaDataException(
                "Theme '$themeId' is configured with a source whose metadata.yaml declares id '{$themeMetaData->getId()}'"
            );
        }

        return $themeMetaData;
    }
}
