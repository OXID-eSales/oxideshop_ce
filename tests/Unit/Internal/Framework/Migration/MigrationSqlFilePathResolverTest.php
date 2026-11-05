<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFilePathResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class MigrationSqlFilePathResolverTest extends TestCase
{
    public function testResolveKeepsAbsoluteFilePath(): void
    {
        $this->assertSame(
            '/var/www/migration.sql',
            (new MigrationSqlFilePathResolver())->resolve('/var/www/migration.sql')
        );
    }

    public function testResolveMakesRelativeFilePathAbsolute(): void
    {
        $this->assertSame(
            Path::join((string) getcwd(), 'migration.sql'),
            (new MigrationSqlFilePathResolver())->resolve('migration.sql')
        );
    }

    public function testResolveGeneratesFileNameInsideDirectory(): void
    {
        $directory = Path::canonicalize(sys_get_temp_dir());

        $this->assertMatchesRegularExpression(
            '#^' . preg_quote($directory, '#') . '/doctrine_migration_\d{14}\.sql$#',
            (new MigrationSqlFilePathResolver())->resolve($directory)
        );
    }

    public function testResolveGeneratesFileNameInsideWorkingDirectory(): void
    {
        $this->assertMatchesRegularExpression(
            '#^' . preg_quote((string) getcwd(), '#') . '/doctrine_migration_\d{14}\.sql$#',
            (new MigrationSqlFilePathResolver())->resolve('.')
        );
    }
}
