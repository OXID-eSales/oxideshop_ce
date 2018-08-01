<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao;

use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\DataObject\ProjectConfiguration;

/**
 * @internal
 */
class JsonFileDao implements ProjectConfigurationDaoInterface
{
    /**
     * JsonFileStorage constructor.
     *
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
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
     * @param ProjectConfiguration $configuration
     */
    public function persistConfiguration(ProjectConfiguration $configuration)
    {
    }
}
