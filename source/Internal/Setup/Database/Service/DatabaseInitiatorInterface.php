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
     * @throws InitiateDatabaseException
     */
    public function initiateDatabase(): void;
}
