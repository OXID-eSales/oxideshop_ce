<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityChecker;
use PHPUnit\Framework\TestCase;

final class MigrationAvailabilityCheckerTest extends TestCase
{
    public function testReturnsTrueWhenAllMigrationDirectoriesExist(): void
    {
        $this->assertTrue(
            (new MigrationAvailabilityChecker())
                ->hasMigrationDirectories(__DIR__ . '/Fixtures/Availability/WithDirectory/migrations.yml')
        );
    }

    public function testReturnsFalseWhenMigrationDirectoryIsMissing(): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())
                ->hasMigrationDirectories(__DIR__ . '/Fixtures/Availability/MissingDirectory/migrations.yml')
        );
    }

    public function testReturnsFalseWhenOneOfSeveralMigrationDirectoriesIsMissing(): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())
                ->hasMigrationDirectories(__DIR__ . '/Fixtures/Availability/PartiallyMissing/migrations.yml')
        );
    }
}
