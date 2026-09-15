<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ProjectMigrationPathProvider;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ProjectMigrationPathProviderTest extends TestCase
{
    use ContainerTrait;

    public function testGetMigrationConfigPathResolvesToTheRealProjectMigrationsFile(): void
    {
        $configPath = $this->get(ProjectMigrationPathProvider::class)->getMigrationConfigPath();

        $this->assertFileExists($configPath);
        $this->assertStringEndsWith('/source/migration/project_migrations.yml', $configPath);
    }
}
