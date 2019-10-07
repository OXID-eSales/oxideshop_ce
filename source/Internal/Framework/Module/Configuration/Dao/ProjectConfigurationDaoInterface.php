<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use \OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;

interface ProjectConfigurationDaoInterface
{
    /**
     * @return ProjectConfiguration
     */
    public function getConfiguration(): ProjectConfiguration;

    /**
     * @param ProjectConfiguration $configuration
     */
    public function save(ProjectConfiguration $configuration);

    /**
     * @return bool
     */
    public function isConfigurationEmpty(): bool;
}
