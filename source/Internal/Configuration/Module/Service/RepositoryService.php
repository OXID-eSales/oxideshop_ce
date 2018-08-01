<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;

/**
 * @internal
 */
class RepositoryService implements RepositoryServiceInterface
{
    /**
     * RepositoryService constructor.
     *
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     */
    public function __construct(ProjectConfigurationDaoInterface $projectConfigurationDao)
    {
    }

    /**
     * @return ProjectConfiguration
     */
    public function getConfiguration(): ProjectConfiguration
    {
        return new ProjectConfiguration();
    }

    /**
     * @param  ProjectConfiguration $configuration
     */
    public function saveConfiguration(ProjectConfiguration $configuration)
    {
    }
}
