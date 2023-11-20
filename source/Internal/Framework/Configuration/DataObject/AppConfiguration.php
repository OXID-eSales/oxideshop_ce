<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

class AppConfiguration
{
    private DatabaseConfiguration $databaseConfiguration;

    public function getDatabaseConfiguration(): DatabaseConfiguration
    {
        return $this->databaseConfiguration;
    }

    public function setDatabaseConfiguration(DatabaseConfiguration $databaseConfiguration): void
    {
        $this->databaseConfiguration = $databaseConfiguration;
    }

}
