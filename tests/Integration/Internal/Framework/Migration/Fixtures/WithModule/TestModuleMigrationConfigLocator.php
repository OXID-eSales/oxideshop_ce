<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\WithModule;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Migration\ModuleMigrationConfigLocatorInterface;

class TestModuleMigrationConfigLocator implements ModuleMigrationConfigLocatorInterface
{
    public function getMigrationConfigPaths(): array
    {
        return ['test-module' => __DIR__ . '/../MultipleSets/migration/migrations.yml'];
    }
}
