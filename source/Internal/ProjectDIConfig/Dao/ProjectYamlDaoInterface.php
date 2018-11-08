<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\DataObject\DIConfigWrapper;

/**
 * @internal
 */
interface ProjectYamlDaoInterface
{

    const PROJECT_FILE_NAME = 'project.yaml';

    /**
     * @param string $path
     *
     * @return DIConfigWrapper
     */
    public function loadDIConfigFile(string $path): DIConfigWrapper;

    /**
     * @return DIConfigWrapper
     */
    public function loadProjectConfigFile(): DIConfigWrapper;

    /**
     * @param DIConfigWrapper $config
     */
    public function saveProjectConfigFile(DIConfigWrapper $config);
}
