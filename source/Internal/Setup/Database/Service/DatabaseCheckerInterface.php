<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;

interface DatabaseCheckerInterface
{
    /**
     * @throws DatabaseExistsAndNotEmptyException
     */
    public function canCreateDatabase(
        string $host,
        int $port,
        string $user,
        string $password,
        string $name
    ): void;
}
