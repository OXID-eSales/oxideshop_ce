<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class SettingModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var SettingDaoInterface
     */
    private $settingDao;

    /**
     * SettingModuleSettingHandler constructor.
     * @param SettingDaoInterface $shopModuleSettingDao
     */
    public function __construct(SettingDaoInterface $shopModuleSettingDao)
    {
        $this->settingDao = $shopModuleSettingDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($configuration->getModuleSettings() as $setting) {
            $this->settingDao->save($setting, $configuration->getId(), $shopId);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($configuration->getModuleSettings() as $setting) {
            $this->settingDao->delete($setting, $configuration->getId(), $shopId);
        }
    }
}
