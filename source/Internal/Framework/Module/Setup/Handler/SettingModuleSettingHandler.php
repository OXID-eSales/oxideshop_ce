<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;

class SettingModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var SettingDaoInterface
     */
    private $settingDao;

    /**
     * SettingModuleSettingHandler constructor.
     */
    public function __construct(SettingDaoInterface $shopModuleSettingDao)
    {
        $this->settingDao = $shopModuleSettingDao;
    }

    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($configuration->getModuleSettings() as $setting) {
            $this->settingDao->save($setting, $configuration->getId(), $shopId);
        }
    }

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($configuration->getModuleSettings() as $setting) {
            $this->settingDao->delete($setting, $configuration->getId(), $shopId);
        }
    }
}
