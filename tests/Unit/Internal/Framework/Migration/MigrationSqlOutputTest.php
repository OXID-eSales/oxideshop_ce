<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class MigrationSqlOutputTest extends TestCase
{
    private Filesystem $filesystem;
    private MigrationSqlOutput $output;
    private string $directory;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->output = new MigrationSqlOutput($this->filesystem);
        $this->directory = Path::join(sys_get_temp_dir(), uniqid('oxid_sql_output_test_', true));
        $this->filesystem->mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->directory);
    }

    public function testResolveFileReturnsNullWhenWriteSqlIsNotRequested(): void
    {
        $this->assertNull($this->output->resolveFile([]));
        $this->assertNull($this->output->resolveFile(['--write-sql' => false]));
    }

    public function testResolveFileKeepsFilePath(): void
    {
        $file = $this->directory . '/out.sql';

        $this->assertSame($file, $this->output->resolveFile(['--write-sql' => $file]));
    }

    public function testResolveFileGeneratesFileNameInsideDirectory(): void
    {
        $file = $this->output->resolveFile(['--write-sql' => $this->directory]);

        $this->assertMatchesRegularExpression('#/doctrine_migration_\d{14}\.sql$#', $file);
        $this->assertSame(realpath($this->directory), dirname($file));
    }

    public function testResolveFileFallsBackToWorkingDirectoryWithoutValue(): void
    {
        $file = $this->output->resolveFile(['--write-sql' => null]);

        $this->assertSame(realpath((string) getcwd()), dirname($file));
    }

    public function testPrepareCreatesEmptyFileAndTruncatesExistingOne(): void
    {
        $file = Path::join($this->directory, 'out.sql');
        $this->filesystem->dumpFile($file, 'old;');

        $this->assertTrue($this->output->prepare($file));
        $this->assertSame('', file_get_contents($file));
    }

    public function testPrepareFailsWhenFileCannotBeWritten(): void
    {
        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('dumpFile')->willThrowException(new IOException('Failed to write file.'));

        $this->assertFalse(new MigrationSqlOutput($filesystem)->prepare(Path::join($this->directory, 'out.sql')));
    }
}
