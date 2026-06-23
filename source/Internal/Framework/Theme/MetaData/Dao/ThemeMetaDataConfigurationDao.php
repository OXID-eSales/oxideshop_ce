<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\DataMapper\ThemeMetaDataMapperInterface;

readonly class ThemeMetaDataConfigurationDao implements ThemeMetaDataConfigurationDaoInterface
{
    public function __construct(
        private ThemeMetaDataProviderInterface $themeMetaDataProvider,
        private ThemeMetaDataMapperInterface $themeMetaDataMapper
    ) {
    }

    public function get(string $themePath): ThemeConfiguration
    {
        return $this->themeMetaDataMapper->fromData(
            $this->themeMetaDataProvider->getData($themePath)
        );
    }
}
