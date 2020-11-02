<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;

interface ProjectConfigurationDaoInterface
{
    public function getConfiguration(): ProjectConfiguration;

    public function save(ProjectConfiguration $configuration);

    public function isConfigurationEmpty(): bool;
}
