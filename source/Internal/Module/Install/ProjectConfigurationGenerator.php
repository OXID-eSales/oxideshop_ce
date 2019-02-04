<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ProjectConfigurationGenerator implements ProjectConfigurationGeneratorInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * DefaultProjectConfigurationGenerator constructor.
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     * @param BasicContextInterface            $context
     */
    public function __construct(ProjectConfigurationDaoInterface $projectConfigurationDao, BasicContextInterface $context)
    {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->context = $context;
    }

    /**
     * Generates default project configuration.
     */
    public function generate()
    {
        $projectConfiguration = new ProjectConfiguration();

        $projectConfiguration->addEnvironmentConfiguration(
            $this->context->getEnvironment(),
            $this->createEnvironmentConfiguration()
        );

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }

    /**
     * @return EnvironmentConfiguration
     */
    private function createEnvironmentConfiguration(): EnvironmentConfiguration
    {
        $environmentConfiguration = new EnvironmentConfiguration();

        foreach ($this->context->getAllShopIds() as $shopId) {
            $environmentConfiguration->addShopConfiguration($shopId, new ShopConfiguration());
        }

        return $environmentConfiguration;
    }
}
