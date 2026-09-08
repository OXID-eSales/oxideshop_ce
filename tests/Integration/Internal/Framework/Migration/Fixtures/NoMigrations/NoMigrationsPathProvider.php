<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\NoMigrations;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationsNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationPathProviderInterface;

class NoMigrationsPathProvider implements MigrationPathProviderInterface
{
    public function getMigrationConfigPath(): string
    {
        throw new MigrationsNotFoundException();
    }
}
