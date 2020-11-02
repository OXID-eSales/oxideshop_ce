<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;

interface ProjectYamlDaoInterface
{
    public function loadDIConfigFile(string $path): DIConfigWrapper;

    public function loadProjectConfigFile(): DIConfigWrapper;

    public function saveProjectConfigFile(DIConfigWrapper $config);
}
