<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

class SystemConfiguration
{
    private DatabaseConfiguration $databaseConfiguration;
    private FilesystemConfiguration $filesystemConfiguration;

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        return $this->databaseConfiguration;
    }

    public function setDatabaseConfiguration(DatabaseConfiguration $databaseConfiguration): void
    {
        $this->databaseConfiguration = $databaseConfiguration;
    }

    public function getFilesystemConfiguration(): FilesystemConfiguration
    {
        return $this->filesystemConfiguration;
    }

    public function setFilesystemConfiguration(FilesystemConfiguration $filesystemConfiguration): void
    {
        $this->filesystemConfiguration = $filesystemConfiguration;
    }

}
