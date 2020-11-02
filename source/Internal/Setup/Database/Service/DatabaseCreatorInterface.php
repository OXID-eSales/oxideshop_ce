<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseConnectionException;

interface DatabaseCreatorInterface
{
    /**
     * @throws DatabaseConnectionException
     */
    public function createDatabase(string $host, int $port, string $username, string $password, string $name): void;
}
