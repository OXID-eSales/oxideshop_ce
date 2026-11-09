<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\WriteSqlMigrationExecutor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class WriteSqlMigrationExecutorTest extends TestCase
{
    private string $sqlDirectory;
    private string $workingDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlDirectory = Path::join(sys_get_temp_dir(), uniqid('oxid-migration-sql-', true));
        (new Filesystem())->mkdir($this->sqlDirectory);
        $this->workingDirectory = (string) getcwd();
        chdir($this->sqlDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->workingDirectory);
        (new Filesystem())->remove($this->sqlDirectory);

        parent::tearDown();
    }

    public function testEmptiesGivenFileAndPassesItToMigrations(): void
    {
        $sqlFile = Path::join($this->sqlDirectory, 'migration.sql');
        (new Filesystem())->dumpFile($sqlFile, '-- previous run');
        $output = new NullOutput();

        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with(['--dry-run' => true, '--write-sql' => $sqlFile], $output)
            ->willReturn(3);

        $status = (new WriteSqlMigrationExecutor($migrationExecutor))
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], $output);

        $this->assertSame(3, $status);
        $this->assertSame('', file_get_contents($sqlFile));
    }

    public function testResolvesRelativeFilePathAgainstWorkingDirectory(): void
    {
        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with(['--write-sql' => Path::join($this->sqlDirectory, 'migration.sql')]);

        (new WriteSqlMigrationExecutor($migrationExecutor))->executeWithOptions(['--write-sql' => 'migration.sql']);

        $this->assertFileExists(Path::join($this->sqlDirectory, 'migration.sql'));
    }

    #[DataProvider('directoryPathDataProvider')]
    public function testCreatesGeneratedFileWhenDirectoryIsGiven(?string $writeSqlPath): void
    {
        $passedOptions = [];
        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->willReturnCallback(static function (array $options) use (&$passedOptions): int {
                $passedOptions = $options;
                return 0;
            });

        (new WriteSqlMigrationExecutor($migrationExecutor))->executeWithOptions(['--write-sql' => $writeSqlPath]);

        $this->assertMatchesRegularExpression(
            '#^' . preg_quote($this->sqlDirectory, '#') . '/doctrine_migration_\d{14}\.sql$#',
            $passedOptions['--write-sql']
        );
        $this->assertFileExists($passedOptions['--write-sql']);
    }

    public static function directoryPathDataProvider(): array
    {
        return [
            'directory given' => ['.'],
            'no path given' => [null],
        ];
    }

    #[DataProvider('optionsWithoutSqlFileDataProvider')]
    public function testKeepsOptionsWhenNoSqlFileIsRequested(array $options): void
    {
        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->expects($this->once())->method('executeWithOptions')->with($options);

        (new WriteSqlMigrationExecutor($migrationExecutor))->executeWithOptions($options);

        $this->assertSame([], array_diff((array) scandir($this->sqlDirectory), ['.', '..']));
    }

    public static function optionsWithoutSqlFileDataProvider(): array
    {
        return [
            'option absent' => [['--dry-run' => true]],
            'option disabled' => [['--write-sql' => false]],
            'option set to true' => [['--write-sql' => true]],
        ];
    }

    public function testThrowsAndRunsNoMigrationWhenFileCannotBeCreated(): void
    {
        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->expects($this->never())->method('executeWithOptions');

        $this->expectException(RuntimeException::class);

        (new WriteSqlMigrationExecutor($migrationExecutor))
            ->executeWithOptions(['--write-sql' => Path::join($this->sqlDirectory, 'missing', 'migration.sql')]);
    }

    public function testPrintsFilePathInVerboseMode(): void
    {
        $sqlFile = Path::join($this->sqlDirectory, 'migration.sql');
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        (new WriteSqlMigrationExecutor($this->createStub(ConfigurableMigrationExecutorInterface::class)))
            ->executeWithOptions(['--write-sql' => $sqlFile], $output);

        $this->assertStringContainsString(sprintf('Writing migration SQL to "%s"', $sqlFile), $output->fetch());
    }
}
