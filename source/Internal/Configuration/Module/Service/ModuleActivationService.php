<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\ProjectConfigurationDaoInterface;
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
     * @var ModuleDataTransferServiceInterface
     */
    private $moduleDataToShopConfigurationTransferService;

    /**
     * ModuleActivationService constructor.
     *
     * @param ProjectConfigurationDaoInterface   $projectConfigurationDao
     * @param ModuleDataTransferServiceInterface $moduleDataToShopConfigurationTransferService
     */
    public function __construct(
        ProjectConfigurationDaoInterface $projectConfigurationDao,
        ModuleDataTransferServiceInterface $moduleDataToShopConfigurationTransferService
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->moduleDataToShopConfigurationTransferService = $moduleDataToShopConfigurationTransferService;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->getModuleConfiguration($moduleId, $shopId);

        $this->moduleDataToShopConfigurationTransferService->transfer(
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
}
