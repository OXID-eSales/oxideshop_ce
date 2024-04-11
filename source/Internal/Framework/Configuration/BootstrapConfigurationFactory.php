<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao\SystemConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\SystemConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\BootstrapLocator;

class BootstrapConfigurationFactory
{
    public function create(): SystemConfiguration
    {
        $this->initEnvironment();

        return (new SystemConfigurationDao())->get();
    }

    private function initEnvironment(): void
    {
        $projectRootDirectory = (new BootstrapLocator())->getProjectRoot();
        (new DotenvLoader($projectRootDirectory))->loadEnvironmentVariables();
    }
}
