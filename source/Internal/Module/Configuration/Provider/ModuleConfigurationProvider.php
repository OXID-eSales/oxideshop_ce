<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ModuleConfigurationProvider implements ModuleConfigurationProviderInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * ModuleConfigurationProvider constructor.
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     */
    public function __construct(ProjectConfigurationDaoInterface $projectConfigurationDao)
    {
        $this->projectConfigurationDao = $projectConfigurationDao;
    }

    /**
     * @param string $moduleId
     * @param string $environment
     * @param int    $shopId
     * @return ModuleConfiguration
     */
    public function getModuleConfiguration(string $moduleId, string $environment, int $shopId): ModuleConfiguration
    {
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        return $projectConfiguration
            ->getEnvironmentConfiguration($environment)
            ->getShopConfiguration($shopId)
            ->getModuleConfiguration($moduleId);
    }
}
