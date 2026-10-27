<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlOutputException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlOutput;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class MigrationSqlOutputTest extends TestCase
{
    private Filesystem $filesystem;
    private string $directory;
    private string $file;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->directory = Path::join(sys_get_temp_dir(), uniqid('oxid_sql_output_test_', true));
        $this->filesystem->mkdir($this->directory);
        $this->file = Path::join($this->directory, 'out.sql');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->directory);
    }

    public function testRunsMigrationsWithUnchangedOptionsWhenWriteSqlIsAbsent(): void
    {
        $this->assertSame(['--dry-run' => true], $this->optionsReceivedByCollectedMigrations(['--dry-run' => true]));
    }

    public function testRunsMigrationsWithUnchangedOptionsWhenWriteSqlIsDisabled(): void
    {
        $this->assertSame(['--write-sql' => false], $this->optionsReceivedByCollectedMigrations(['--write-sql' => false]));
    }

    public function testEmptiesTargetFileBeforeMigrations(): void
    {
        $this->filesystem->dumpFile($this->file, 'old;');

        new MigrationSqlOutput($this->filesystem)->collect(['--write-sql' => $this->file], fn(): int => 0);

        $this->assertSame('', file_get_contents($this->file));
    }

    public function testCollectsSqlOfEveryCapturedMigrationIntoOneFile(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);

        $exitCode = $sqlOutput->collect(
            ['--write-sql' => $this->file],
            function (array $options) use ($sqlOutput): int {
                $sqlOutput->capture($options, $this->writing('first;', 0));
                return $sqlOutput->capture($options, $this->writing('second;', 3));
            }
        );

        $this->assertSame(3, $exitCode);
        $this->assertSame('first;second;', file_get_contents($this->file));
    }

    public function testNestedCollectionJoinsRunningCollection(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);

        $sqlOutput->collect(
            ['--write-sql' => $this->file],
            function (array $options) use ($sqlOutput): int {
                $sqlOutput->capture($options, $this->writing('first;', 0));
                return $sqlOutput->collect(
                    $options,
                    fn(array $options): int => $sqlOutput->capture($options, $this->writing('second;', 0))
                );
            }
        );

        $this->assertSame('first;second;', file_get_contents($this->file));
    }

    public function testKeepsOtherOptions(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);
        $received = null;

        $sqlOutput->collect(
            ['--write-sql' => $this->file, '--dry-run' => true],
            function (array $options) use ($sqlOutput, &$received): int {
                return $sqlOutput->capture(
                    $options,
                    function (array $options) use (&$received): int {
                        $received = $options;
                        return 0;
                    }
                );
            }
        );

        $this->assertTrue($received['--dry-run']);
    }

    public function testGeneratesFileNameInsideDirectory(): void
    {
        $received = $this->resolvedFileFor($this->directory);

        $this->assertMatchesRegularExpression('#/doctrine_migration_\d{14}\.sql$#', $received);
        $this->assertSame($this->directory, dirname($received));
    }

    public function testGeneratesFileNameInsideWorkingDirectoryWithoutPath(): void
    {
        $workingDirectory = getcwd();
        chdir($this->directory);
        try {
            $received = $this->resolvedFileFor(null);
        } finally {
            chdir($workingDirectory);
        }

        $this->assertSame(realpath($this->directory), dirname($received));
    }

    public function testCapturePassesOptionsThroughWhenNotCollecting(): void
    {
        $received = null;

        $exitCode = new MigrationSqlOutput($this->filesystem)->capture(
            ['--write-sql' => $this->file],
            function (array $options) use (&$received): int {
                $received = $options;
                return 3;
            }
        );

        $this->assertSame(3, $exitCode);
        $this->assertSame(['--write-sql' => $this->file], $received);
    }

    public function testCapturePassesOptionsThroughWhenRunDoesNotRequestSql(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);
        $received = null;

        $sqlOutput->collect(
            ['--write-sql' => $this->file],
            function () use ($sqlOutput, &$received): int {
                return $sqlOutput->capture(
                    ['--dry-run' => true],
                    function (array $options) use (&$received): int {
                        $received = $options;
                        return 0;
                    }
                );
            }
        );

        $this->assertSame(['--dry-run' => true], $received);
    }

    public function testStopsCollectingWhenMigrationsThrow(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);
        try {
            $sqlOutput->collect(['--write-sql' => $this->file], fn(): never => throw new RuntimeException());
        } catch (RuntimeException) {
        }
        $received = null;

        $sqlOutput->capture(
            ['--write-sql' => $this->file],
            function (array $options) use (&$received): int {
                $received = $options['--write-sql'];
                return 0;
            }
        );

        $this->assertSame($this->file, $received);
    }

    public function testRemovesTemporaryFileWhenMigrationThrows(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);
        $temporaryFile = null;

        try {
            $sqlOutput->collect(
                ['--write-sql' => $this->file],
                function (array $options) use ($sqlOutput, &$temporaryFile): int {
                    return $sqlOutput->capture(
                        $options,
                        function (array $options) use (&$temporaryFile): never {
                            $temporaryFile = $options['--write-sql'];
                            throw new RuntimeException();
                        }
                    );
                }
            );
        } catch (RuntimeException) {
        }

        $this->assertFileDoesNotExist($temporaryFile);
    }

    public function testFailsWhenTargetFileCannotBeEmptied(): void
    {
        $this->expectException(MigrationSqlOutputException::class);

        new MigrationSqlOutput($this->failingFilesystem('dumpFile'))
            ->collect(['--write-sql' => $this->file], fn(): int => 0);
    }

    public function testRunsNoMigrationWhenTargetFileCannotBeEmptied(): void
    {
        $migrationsRan = false;

        try {
            new MigrationSqlOutput($this->failingFilesystem('dumpFile'))->collect(
                ['--write-sql' => $this->file],
                function () use (&$migrationsRan): int {
                    $migrationsRan = true;
                    return 0;
                }
            );
        } catch (MigrationSqlOutputException) {
        }

        $this->assertFalse($migrationsRan);
    }

    public function testFailsWhenCapturedSqlCannotBeAppended(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->failingFilesystem('appendToFile'));

        $this->expectException(MigrationSqlOutputException::class);

        $sqlOutput->collect(
            ['--write-sql' => $this->file],
            fn(array $options): int => $sqlOutput->capture($options, $this->writing('sql;', 0))
        );
    }

    public function testFailsWhenCapturedSqlCannotBeRead(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->filesystem);

        $this->expectException(MigrationSqlOutputException::class);

        $sqlOutput->collect(
            ['--write-sql' => $this->file],
            fn(array $options): int => $sqlOutput->capture(
                $options,
                static function (array $options): int {
                    unlink($options['--write-sql']);
                    return 0;
                }
            )
        );
    }

    public function testRemovesTemporaryFileWhenCapturedSqlCannotBeAppended(): void
    {
        $sqlOutput = new MigrationSqlOutput($this->failingFilesystem('appendToFile'));
        $temporaryFile = null;

        try {
            $sqlOutput->collect(
                ['--write-sql' => $this->file],
                function (array $options) use ($sqlOutput, &$temporaryFile): int {
                    return $sqlOutput->capture(
                        $options,
                        function (array $options) use (&$temporaryFile): int {
                            $temporaryFile = $options['--write-sql'];
                            return 0;
                        }
                    );
                }
            );
        } catch (MigrationSqlOutputException) {
        }

        $this->assertFileDoesNotExist($temporaryFile);
    }

    private function optionsReceivedByCollectedMigrations(array $options): ?array
    {
        $received = null;
        new MigrationSqlOutput($this->filesystem)->collect(
            $options,
            function (array $options) use (&$received): int {
                $received = $options;
                return 0;
            }
        );

        return $received;
    }

    private function resolvedFileFor(?string $path): string
    {
        return $this->optionsReceivedByCollectedMigrations(['--write-sql' => $path])['--write-sql'];
    }

    private function writing(string $sql, int $exitCode): callable
    {
        return static function (array $options) use ($sql, $exitCode): int {
            file_put_contents($options['--write-sql'], $sql);
            return $exitCode;
        };
    }

    private function failingFilesystem(string $method): Filesystem
    {
        $filesystem = $this->createPartialMock(Filesystem::class, [$method]);
        $filesystem->expects($this->once())->method($method)->willThrowException(new IOException('Failed to write file.'));

        return $filesystem;
    }
}
