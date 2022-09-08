<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleEnvironmentConfigurationExtender implements ModuleConfigurationExtenderInterface
{
    public function __construct(
        private ModuleEnvironmentConfigurationDaoInterface $moduleEnvironmentConfigurationDao,
        private EventDispatcherInterface                   $eventDispatcher
    ) {
    }

    public function extend(ModuleConfiguration $moduleConfiguration, int $shopId): ModuleConfiguration
    {
        $environmentData = $this->moduleEnvironmentConfigurationDao->get($moduleConfiguration->getId(), $shopId);

        if (isset($environmentData['moduleSettings'])) {
            foreach ($environmentData['moduleSettings'] as $settingId => $environmentSetting) {
                if (!$moduleConfiguration->hasModuleSetting($settingId)) {
                    $this->processOrphanSetting($shopId, $moduleConfiguration->getId(), $settingId);
                    continue;
                }
                $this->mergeEnvironmentSetting($moduleConfiguration->getModuleSetting($settingId), $environmentSetting);
            }

        }

        return $moduleConfiguration;
    }

    public function mergeEnvironmentSetting(Setting $originalSetting, array $environmentSetting): void
    {
        if (isset($environmentSetting['value'])) {
            $originalSetting->setValue($environmentSetting['value']);
        }

        if (isset($environmentSetting['group'])) {
            $originalSetting->setGroupName($environmentSetting['group']);
        }

        if (isset($environmentSetting['position'])) {
            $originalSetting->setPositionInGroup($environmentSetting['position']);
        }

        if (isset($environmentSetting['constraints'])) {
            $originalSetting->setConstraints($environmentSetting['constraints']);
        }
    }

    private function processOrphanSetting(int $shopId, string $moduleId, string $orphanSettingId): void
    {
        $this->eventDispatcher->dispatch(
            new ShopEnvironmentWithOrphanSettingEvent($shopId, $moduleId, $orphanSettingId)
        );
    }
}
