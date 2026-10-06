<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationsNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ProjectMigrationPathProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ProjectMigrationPathProviderTest extends TestCase
{
    use ContainerTrait;

    public function testGetMigrationConfigPathResolvesToTheRealProjectMigrationsFile(): void
    {
        $configPath = $this->createProvider()->getMigrationConfigPath();

        $this->assertFileExists($configPath);
        $this->assertStringEndsWith('/source/migration/project_migrations.yml', $configPath);
    }

    public function testGetMigrationConfigPathThrowsForShippedEmptyProjectMigrations(): void
    {
        $this->expectException(MigrationsNotFoundException::class);

        $this->get(ProjectMigrationPathProvider::class)->getMigrationConfigPath();
    }

    private function createProvider(): ProjectMigrationPathProvider
    {
        $availabilityChecker = $this->createStub(MigrationAvailabilityCheckerInterface::class);
        $availabilityChecker->method('hasMigrations')->willReturn(true);

        return new ProjectMigrationPathProvider($this->get(BasicContextInterface::class), $availabilityChecker);
    }
}
