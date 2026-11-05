<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlFileNotWritableException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFileWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class MigrationSqlFileWriterTest extends TestCase
{
    private string $sqlDirectory;
    private string $sqlFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlDirectory = Path::join(sys_get_temp_dir(), uniqid('oxid-migration-sql-', true));
        $this->sqlFile = Path::join($this->sqlDirectory, 'migration.sql');
        (new Filesystem())->mkdir($this->sqlDirectory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->sqlDirectory);

        parent::tearDown();
    }

    public function testClearEmptiesExistingFile(): void
    {
        (new Filesystem())->dumpFile($this->sqlFile, '-- previous run');

        (new MigrationSqlFileWriter())->clear($this->sqlFile);

        $this->assertSame('', file_get_contents($this->sqlFile));
    }

    public function testClearCreatesMissingFile(): void
    {
        (new MigrationSqlFileWriter())->clear($this->sqlFile);

        $this->assertSame('', file_get_contents($this->sqlFile));
    }

    #[DataProvider('unwritablePathDataProvider')]
    public function testClearThrowsWhenPathIsNotWritable(string $relativePath): void
    {
        $this->expectException(MigrationSqlFileNotWritableException::class);

        (new MigrationSqlFileWriter())->clear(Path::join($this->sqlDirectory, $relativePath));
    }

    public static function unwritablePathDataProvider(): array
    {
        return [
            'file in missing directory' => ['missing/migration.sql'],
            'directory' => ['.'],
        ];
    }

    public function testAppendAddsSqlToTheEndOfTheFile(): void
    {
        (new Filesystem())->dumpFile($this->sqlFile, "-- first set\n");

        (new MigrationSqlFileWriter())->append($this->sqlFile, "-- second set\n");

        $this->assertSame("-- first set\n-- second set\n", file_get_contents($this->sqlFile));
    }
}
