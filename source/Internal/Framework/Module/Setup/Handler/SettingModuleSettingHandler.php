<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
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
            $setting
                ->setShopId($shopId)
                ->setModuleId($configuration->getId());

            $this->settingDao->save($setting);
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($configuration->getModuleSettings() as $setting) {
            $setting
                ->setShopId($shopId)
                ->setModuleId($configuration->getId());

            $this->settingDao->delete($setting);
        }
    }
}
