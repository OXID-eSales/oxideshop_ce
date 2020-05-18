<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\InitiateDatabaseException;

interface DatabaseInitiatorInterface
{
    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $name
     * @throws InitiateDatabaseException
     */
    public function initiateDatabase(string $host, int $port, string $username, string $password, string $name): void;
}
