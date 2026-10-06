<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityChecker;
use PHPUnit\Framework\TestCase;

final class MigrationAvailabilityCheckerTest extends TestCase
{
    public function testReturnsTrueWhenMigrationDirectoryContainsMigration(): void
    {
        $this->assertTrue(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/WithMigration/migrations.yml')
        );
    }

    public function testReturnsFalseWhenMigrationDirectoryContainsNoMigration(): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/WithoutMigration/migrations.yml')
        );
    }

    public function testReturnsFalseWhenMigrationDirectoryIsMissing(): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/MissingDirectory/migrations.yml')
        );
    }

    public function testReturnsTrueWhenOneOfSeveralMigrationDirectoriesContainsMigration(): void
    {
        $this->assertTrue(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/PartiallyMissing/migrations.yml')
        );
    }

    public function testReturnsTrueWhenMigrationDirectoryContainsSubdirectory(): void
    {
        $this->assertTrue(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/WithSubdirectory/migrations.yml')
        );
    }

    public function testReturnsFalseWhenConfigFileIsMissing(): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())
                ->hasMigrations(__DIR__ . '/Fixtures/Availability/MissingConfig/migrations.yml')
        );
    }
}
