<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ThemeConfigurationService implements ThemeConfigurationServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeStateServiceInterface $themeStateService,
        private ContextInterface $context,
    ) {
    }

    public function getConfiguration(string $themeId): ThemeConfiguration
    {
        return $this->themeConfigurationDao->get($themeId, $this->context->getCurrentShopId());
    }

    public function getActiveConfiguration(): ThemeConfiguration
    {
        return $this->getConfiguration(
            $this->themeStateService->getActiveThemeId($this->context->getCurrentShopId())
        );
    }

    public function updateSettings(ThemeConfiguration $configuration, array $settingValues): void
    {
        foreach ($settingValues as $name => $value) {
            $configuration->getSettingByName((string) $name)?->setValue($value);
        }

        $this->themeConfigurationDao->save($configuration, $this->context->getCurrentShopId());
    }
}
