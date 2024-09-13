<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;

interface SetupDbConnectionFactoryInterface
{
    public function getServerConnection(DatabaseConfiguration $databaseConfiguration): Connection;

    public function getDatabaseConnection(DatabaseConfiguration $databaseConfiguration): Connection;
}
