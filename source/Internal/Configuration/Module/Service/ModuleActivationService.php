<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Common\Configuration\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ModuleActivationService implements ModuleActivationServiceInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var ModuleConfigurationDataMapperInterface
     */
    private $moduleConfigurationDataMapper;

    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * ModuleActivationService constructor.
     * @param ProjectConfigurationDaoInterface       $projectConfigurationDao
     * @param ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper
     * @param ShopConfigurationSettingDaoInterface   $shopConfigurationSettingDao
     */
    public function __construct(
        ProjectConfigurationDaoInterface        $projectConfigurationDao,
        ModuleConfigurationDataMapperInterface  $moduleConfigurationDataMapper,
        ShopConfigurationSettingDaoInterface    $shopConfigurationSettingDao
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->moduleConfigurationDataMapper = $moduleConfigurationDataMapper;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->getModuleConfiguration($moduleId, $shopId);

        $this->transferModuleConfigurationToShopConfigurationSettings(
            $moduleConfiguration,
            $shopId
        );
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        // TODO: Implement deactivate() method.
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return ModuleConfiguration
     */
    private function getModuleConfiguration(string $moduleId, int $shopId): ModuleConfiguration
    {
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        return $projectConfiguration
            ->getEnvironmentConfiguration('dev')
            ->getShopConfiguration($shopId)
            ->getModuleConfiguration($moduleId);
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function transferModuleConfigurationToShopConfigurationSettings(
        ModuleConfiguration $moduleConfiguration,
        int                 $shopId
    ) {
        $moduleConfigurationData = $this->moduleConfigurationDataMapper->toData($moduleConfiguration);

        foreach ($moduleConfigurationData as $settingName => $settingValue) {
            $shopSetting = $this->shopConfigurationSettingDao->get($settingName, $shopId);
            $shopSetting = array_merge($shopSetting, $settingValue);

            $this->shopConfigurationSettingDao->save($settingName, $shopSetting, $shopId);
        }
    }
}
