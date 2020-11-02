<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

interface DatabaseInstallerInterface
{
    public function install(string $host, int $port, string $user, string $password, string $name): void;
}
