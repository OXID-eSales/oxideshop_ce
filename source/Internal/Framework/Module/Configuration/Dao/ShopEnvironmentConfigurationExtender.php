<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShopEnvironmentConfigurationExtender implements ShopConfigurationExtenderInterface
{
    /** @var ShopEnvironmentConfigurationDaoInterface */
    private $shopEnvironmentConfigurationDao;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var int */
    private $shopId;

    public function __construct(
        ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->shopEnvironmentConfigurationDao = $shopEnvironmentConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @inheritDoc */
    public function getExtendedConfiguration(int $shopId, array $shopConfiguration): array
    {
        $this->shopId = $shopId;
        $environmentData = $this->shopEnvironmentConfigurationDao->get($this->shopId);
        $environmentConfiguration = $this->filterEnvironmentData($shopConfiguration, $environmentData);
        return \array_replace_recursive($shopConfiguration, $environmentConfiguration);
    }

    private function filterEnvironmentData(array $shopConfiguration, array $environmentData): array
    {
        if (empty($environmentData['modules'])) {
            return [];
        }
        unset($environmentData['moduleChains']);
        foreach ($environmentData['modules'] as $moduleId => $moduleConfiguration) {
            if (!isset($shopConfiguration['modules'][$moduleId])) {
                unset($environmentData['modules'][$moduleId]);
                continue;
            }
            if (isset($moduleConfiguration['moduleSettings'])) {
                foreach (\array_keys($moduleConfiguration['moduleSettings']) as $settingId) {
                    if (!isset($shopConfiguration['modules'][$moduleId]['moduleSettings'][$settingId])) {
                        unset($environmentData['modules'][$moduleId]['moduleSettings'][$settingId]);
                        $this->processOrphanSetting($moduleId, $settingId);
                    }
                }
            }
        }
        return $environmentData;
    }

    private function processOrphanSetting(string $moduleId, string $orphanSettingId): void
    {
        $this->eventDispatcher->dispatch(
            ShopEnvironmentWithOrphanSettingEvent::NAME,
            new ShopEnvironmentWithOrphanSettingEvent($this->shopId, $moduleId, $orphanSettingId)
        );
    }
}
