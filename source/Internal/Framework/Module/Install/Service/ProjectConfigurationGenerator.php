<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

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
    public function __construct(
        ProjectConfigurationDaoInterface $projectConfigurationDao,
        BasicContextInterface $context
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->context = $context;
    }

    /**
     * Generates default project configuration.
     */
    public function generate(): void
    {
        $this->projectConfigurationDao->save($this->createProjectConfiguration());
    }

    /**
     * @return ProjectConfiguration
     */
    private function createProjectConfiguration(): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();

        foreach ($this->context->getAllShopIds() as $shopId) {
            $projectConfiguration->addShopConfiguration($shopId, new ShopConfiguration());
        }

        return $projectConfiguration;
    }
}
