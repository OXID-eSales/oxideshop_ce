<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

readonly class ActiveThemeProvider implements ActiveThemeProviderInterface
{
    public function __construct(
        private string $activeThemeId,
        private int $shopId,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function getActiveTheme(): ActiveTheme
    {
        if ($this->activeThemeId === '' || !$this->themeConfigurationDao->exists($this->activeThemeId, $this->shopId)) {
            throw new ActiveThemeNotFoundException();
        }

        return new ActiveTheme(
            $this->activeThemeId,
            $this->themeMetaDataByIdProvider->getById($this->activeThemeId, $this->shopId)->getParentTheme(),
        );
    }
}
