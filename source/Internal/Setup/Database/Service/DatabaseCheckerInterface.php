<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

interface DatabaseCheckerInterface
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $name
     *
     * @return bool
     */
    public function checkIfDatabaseExistsAndNotEmpty(
        string $host,
        int $port,
        string $user,
        string $password,
        string $name
    ): bool;
}
