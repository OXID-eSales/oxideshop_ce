<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use Psr\Log\LoggerInterface;

readonly class ThemeConfigurationResolver implements ThemeConfigurationResolverInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeEnvironmentConfigurationDaoInterface $environmentConfigurationDao,
        private ThemeConfigurationCacheInterface $resolvedConfigurationCache,
        private LoggerInterface $logger,
    ) {
    }

    public function resolve(string $themeId, int $shopId): ThemeConfiguration
    {
        if (!$this->resolvedConfigurationCache->exists($themeId, $shopId)) {
            $this->resolvedConfigurationCache->put(
                $shopId,
                $this->buildResolvedConfiguration($themeId, $shopId)
            );
        }

        return clone $this->resolvedConfigurationCache->get($themeId, $shopId);
    }

    private function buildResolvedConfiguration(string $themeId, int $shopId): ThemeConfiguration
    {
        $configuration = $this->themeConfigurationDao->get($themeId, $shopId);
        $environmentSettingValues = $this->environmentConfigurationDao
            ->get($themeId, $shopId)
            ->getSettingValues();

        foreach ($environmentSettingValues as $name => $value) {
            $setting = $configuration->getSettingByName((string) $name);

            if ($setting === null) {
                $this->logUnknownSetting($themeId, $shopId, (string) $name);
                continue;
            }

            $setting->setValue($value);
        }

        return $configuration;
    }

    private function logUnknownSetting(string $themeId, int $shopId, string $settingName): void
    {
        $this->logger->warning(
            'Environment configuration references an unknown theme setting. The environment value will be ignored.',
            [
                'themeId' => $themeId,
                'shopId' => $shopId,
                'settingName' => $settingName,
            ]
        );
    }
}
