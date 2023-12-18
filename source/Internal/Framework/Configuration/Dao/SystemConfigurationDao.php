<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\FilesystemConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\SystemConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotEnvLoader;

class SystemConfigurationDao
{
    public function get(): SystemConfiguration
    {
        (new DotEnvLoader())->loadEnvironmentVariables();
        $systemConfiguration = new SystemConfiguration();
        $systemConfiguration->setDatabaseConfiguration($this->getDatabaseConfiguration());
        $systemConfiguration->setFilesystemConfiguration($this->getFilesystemConfiguration());

        return $systemConfiguration;
    }

    private function getDatabaseConfiguration(): DatabaseConfiguration
    {
        $configuration = new DatabaseConfiguration();
        $configuration->setDriver(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_DRIVER'));
        $configuration->setCharset(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_CHARSET'));
        $configuration->setHost(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_HOST'));
        $configuration->setPort(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_PORT'));
        $configuration->setName(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_NAME'));
        $configuration->setUser(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_USER'));
        $configuration->setPassword(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_PASSWORD'));
        $configuration->setDriverOptions(
            explode(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_DRIVER_OPTIONS'), ',')
        );
        $configuration->setUnixSocket(getenv('OXID_SYSTEM_CONFIGURATION_DATABASE_UNIX_SOCKET'));

        return $configuration;
    }

    private function getFilesystemConfiguration(): FilesystemConfiguration
    {
        $configuration = new FilesystemConfiguration();
        $configuration->setInstallationDirectory(getenv('OXID_SYSTEM_CONFIGURATION_INSTALLATION_DIRECTORY'));
        $configuration->setTmpDirectory(getenv('OXID_SYSTEM_CONFIGURATION_TMP_DIRECTORY'));

        return $configuration;
    }
}
