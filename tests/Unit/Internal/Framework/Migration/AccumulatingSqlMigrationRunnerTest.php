<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\AccumulatingSqlMigrationRunner;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationRunnerInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class AccumulatingSqlMigrationRunnerTest extends TestCase
{
    private Filesystem $filesystem;
    private string $directory;
    private string $targetFile;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->directory = Path::join(sys_get_temp_dir(), uniqid('oxid_migration_sql_test_', true));
        $this->filesystem->mkdir($this->directory);
        $this->targetFile = Path::join($this->directory, 'out.sql');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->directory);
    }

    public function testAppendsSqlOfEachRunToTargetFile(): void
    {
        $inner = $this->createStub(MigrationRunnerInterface::class);
        $inner->method('run')->willReturnCallback(
            static function (string $path, array $options): int {
                file_put_contents($options['--write-sql'], 'sql;');
                return 0;
            }
        );
        $runner = new AccumulatingSqlMigrationRunner($inner, $this->filesystem);

        $runner->run('first.yml', ['--write-sql' => $this->targetFile]);
        $runner->run('second.yml', ['--write-sql' => $this->targetFile]);

        $this->assertSame('sql;sql;', file_get_contents($this->targetFile));
    }

    public function testPassesOptionsUnchangedWhenWriteSqlIsNotRequested(): void
    {
        $inner = $this->createMock(MigrationRunnerInterface::class);
        $inner->expects($this->once())->method('run')->with('config.yml', ['--dry-run' => true], null)->willReturn(2);

        $exitCode = new AccumulatingSqlMigrationRunner($inner, $this->filesystem)
            ->run('config.yml', ['--dry-run' => true]);

        $this->assertSame(2, $exitCode);
    }

    public function testKeepsOtherOptionsAndReturnsExitCodeOfInnerRunner(): void
    {
        $inner = $this->createMock(MigrationRunnerInterface::class);
        $inner->expects($this->once())
            ->method('run')
            ->with('config.yml', $this->callback(fn(array $o): bool => $o['--dry-run'] === true), null)
            ->willReturn(3);

        $exitCode = new AccumulatingSqlMigrationRunner($inner, $this->filesystem)
            ->run('config.yml', ['--write-sql' => $this->targetFile, '--dry-run' => true]);

        $this->assertSame(3, $exitCode);
    }

    public function testFailsWhenTargetFileCannotBeWritten(): void
    {
        $runner = new AccumulatingSqlMigrationRunner(
            $this->createStub(MigrationRunnerInterface::class),
            $this->unwritableFilesystem()
        );

        $this->assertSame(1, $runner->run('config.yml', ['--write-sql' => $this->targetFile]));
    }

    public function testDeletesTemporaryFileWhenInnerRunnerThrows(): void
    {
        $temporaryFile = null;
        $inner = $this->createStub(MigrationRunnerInterface::class);
        $inner->method('run')->willReturnCallback(
            static function (string $path, array $options) use (&$temporaryFile): never {
                $temporaryFile = $options['--write-sql'];
                throw new RuntimeException('Migration failed.');
            }
        );

        try {
            new AccumulatingSqlMigrationRunner($inner, $this->filesystem)
                ->run('config.yml', ['--write-sql' => $this->targetFile]);
            $this->fail('The exception of the inner runner must not be swallowed.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($temporaryFile);
        }
    }

    public function testDeletesTemporaryFileWhenTargetFileCannotBeWritten(): void
    {
        $temporaryFile = null;
        $inner = $this->createStub(MigrationRunnerInterface::class);
        $inner->method('run')->willReturnCallback(
            static function (string $path, array $options) use (&$temporaryFile): int {
                $temporaryFile = $options['--write-sql'];
                return 0;
            }
        );

        new AccumulatingSqlMigrationRunner($inner, $this->unwritableFilesystem())
            ->run('config.yml', ['--write-sql' => $this->targetFile]);

        $this->assertFileDoesNotExist($temporaryFile);
    }

    private function unwritableFilesystem(): Filesystem
    {
        $filesystem = $this->createPartialMock(Filesystem::class, ['appendToFile']);
        $filesystem->expects($this->once())->method('appendToFile')->willThrowException(new IOException('Failed to write file.'));

        return $filesystem;
    }
}
