<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ThemeMetaDataByIdProvider implements ThemeMetaDataByIdProviderInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataProviderInterface $themeMetaDataProvider,
        private BasicContextInterface $context,
    ) {
    }

    public function get(string $themeId, int $shopId): ThemeMetaData
    {
        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);
        $themePath = Path::join($this->context->getShopRootPath(), $themeConfiguration->getSource());

        return $this->themeMetaDataProvider->get($themePath);
    }
}
