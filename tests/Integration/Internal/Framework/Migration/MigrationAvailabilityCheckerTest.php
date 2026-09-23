<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MigrationAvailabilityCheckerTest extends TestCase
{
    public function testReturnsTrueWhenAllMigrationDirectoriesExist(): void
    {
        $this->assertTrue(
            (new MigrationAvailabilityChecker())->hasMigrationDirectories($this->getFixturePath('WithDirectory'))
        );
    }

    #[DataProvider('unavailableMigrationDirectoriesProvider')]
    public function testReturnsFalseWhenMigrationDirectoriesAreNotAvailable(string $fixture): void
    {
        $this->assertFalse(
            (new MigrationAvailabilityChecker())->hasMigrationDirectories($this->getFixturePath($fixture))
        );
    }

    public static function unavailableMigrationDirectoriesProvider(): array
    {
        return [
            'migration directory is missing' => ['MissingDirectory'],
            'one of several migration directories is missing' => ['PartiallyMissing'],
        ];
    }

    private function getFixturePath(string $fixture): string
    {
        return __DIR__ . "/Fixtures/Availability/$fixture/migrations.yml";
    }
}
