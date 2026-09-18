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

class ActiveThemeProvider implements ActiveThemeProviderInterface
{
    /**
     * @var array<int, string>
     */
    private array $activeThemeIds = [];

    public function __construct(
        private readonly ThemeConfigurationDaoInterface $themeConfigurationDao,
        private readonly ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function getActiveThemeId(int $shopId): string
    {
        if (isset($this->activeThemeIds[$shopId])) {
            return $this->activeThemeIds[$shopId];
        }

        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->isActivated()) {
                return $this->activeThemeIds[$shopId] = $themeConfiguration->getId();
            }
        }

        throw new ActiveThemeNotFoundException();
    }

    public function getActiveTheme(int $shopId): ActiveTheme
    {
        $activeThemeId = $this->getActiveThemeId($shopId);

        return new ActiveTheme(
            $activeThemeId,
            $this->themeMetaDataByIdProvider->getById($activeThemeId, $shopId)->getParentTheme(),
        );
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        $themeConfigurations = $this->themeConfigurationDao->getAll($shopId);

        return isset($themeConfigurations[$themeId]) && $themeConfigurations[$themeId]->isActivated();
    }
}
