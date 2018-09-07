<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ModuleDataToShopConfigurationTransferService implements ModuleDataTransferServiceInterface
{
    /**
     * @var ModuleConfigurationDataMapperInterface
     */
    private $moduleConfigurationDataMapper;

    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ModuleDataToShopConfigurationTransferService constructor.
     * @param ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper
     * @param ShopConfigurationSettingDaoInterface   $shopConfigurationSettingDao
     */
    public function __construct(
        ModuleConfigurationDataMapperInterface  $moduleConfigurationDataMapper,
        ShopConfigurationSettingDaoInterface    $shopConfigurationSettingDao
    ) {
        $this->moduleConfigurationDataMapper = $moduleConfigurationDataMapper;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function transfer(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        $moduleConfigurationData = $this->moduleConfigurationDataMapper->toData($moduleConfiguration);

        foreach ($moduleConfigurationData as $settingName => $settingValue) {
            $this->addModuleSettingToShopConfiguration(
                $settingName,
                $settingValue,
                $shopId
            );
        }
    }

    /**
     * @param string $settingName
     * @param mixed  $settingValue
     * @param int    $shopId
     */
    private function addModuleSettingToShopConfiguration(string $settingName, $settingValue, int $shopId)
    {
        $shopSetting = $this->shopConfigurationSettingDao->get($settingName, $shopId) ?? [];
        $shopSetting = array_merge($shopSetting, $settingValue);

        $this->shopConfigurationSettingDao->save($settingName, $shopSetting, $shopId);
    }
}
