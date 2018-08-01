<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\ModuleConfiguration\DataObject\ProjectConfiguration;

/**
 * @internal
 */
interface RepositoryServiceInterface
{
    /**
     * @return ProjectConfiguration
     */
    public function getConfiguration(): ProjectConfiguration;

    /**
     * @param ProjectConfiguration $configuration
     */
    public function saveConfiguration(ProjectConfiguration $configuration);
}
