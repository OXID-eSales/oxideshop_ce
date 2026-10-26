<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationsNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationPathProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationRunnerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ProjectMigrationPathProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\TaggedMigrationExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Fixtures\MultipleSets\SecondMigrationPathProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

final class TaggedMigrationExecutorTest extends TestCase
{
    use ContainerTrait;

    private array $temporaryDirectories = [];

    public function testMigrationIsExecuted(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();

        $status = $this->get(TaggedMigrationExecutor::class)->executeWithOptions([], new NullOutput());

        $this->assertSame(0, $status);
        $this->assertMigrationWasTracked();
    }

    public function testSkipsProviderWithoutMigrations(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/NoMigrations');
        $this->compileContainer();

        $this->assertSame(0, $this->get(TaggedMigrationExecutor::class)->executeWithOptions([], new NullOutput()));
    }

    public function testSkipsShippedEmptyProjectMigrations(): void
    {
        $migrationRunner = $this->createMock(MigrationRunnerInterface::class);
        $migrationRunner->expects($this->never())->method('run');

        $executor = new TaggedMigrationExecutor(
            [$this->get(ProjectMigrationPathProvider::class)],
            $migrationRunner,
            $this->get(MigrationExitCodeResolverInterface::class),
        );

        $this->assertSame(0, $executor->executeWithOptions([], new NullOutput()));
    }

    public function testReturnsNonZeroExitCodeWhenMigrationFails(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();

        $status = $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--write-sql' => '/nonexistent-dir/x.sql'], new NullOutput());

        $this->assertGreaterThan(0, $status);
    }

    public function testProviderMigrationFailurePropagates(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/Failing');
        $this->compileContainer();

        $this->expectException(\RuntimeException::class);

        $this->get(TaggedMigrationExecutor::class)->executeWithOptions([], new NullOutput());
    }

    public function testFlagsAreForwardedToTheMigration(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();

        $executor = $this->get(TaggedMigrationExecutor::class);
        $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();

        $executor->executeWithOptions([], new NullOutput());
        $this->assertTrue($connection->getSchemaManager()->tablesExist(['test_migration_table']));

        $connection->executeStatement('DROP TABLE test_migration_table');
        $connection->executeStatement('DROP TABLE test_migrations_tracking');

        $executor->executeWithOptions(['--dry-run' => true], new NullOutput());

        $this->assertFalse($connection->getSchemaManager()->tablesExist(['test_migration_table']));
    }

    public function testWrittenSqlContainsMigrationsOfAllProviders(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlFile = tempnam(sys_get_temp_dir(), 'migration_sql_');

        $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());

        $writtenSql = file_get_contents($sqlFile);
        unlink($sqlFile);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $writtenSql);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $writtenSql);
    }

    public function testSqlOfEarlierSetIsNotDuplicatedWhenLaterSetHasNothingToMigrate(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $this->get(MigrationRunnerInterface::class)
            ->run((new SecondMigrationPathProvider())->getMigrationConfigPath(), [], new NullOutput());
        $sqlFile = tempnam(sys_get_temp_dir(), 'migration_sql_');

        $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());

        $writtenSql = file_get_contents($sqlFile);
        unlink($sqlFile);
        $this->assertSame(1, substr_count($writtenSql, 'CREATE TABLE `test_migration_table`'));
    }

    public function testWrittenSqlContainsMigrationsOfProvidersAndModules(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/WithModule');
        $this->compileContainer();
        $sqlFile = tempnam(sys_get_temp_dir(), 'migration_sql_');

        $this->get(ConfigurableMigrationExecutorInterface::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());

        $writtenSql = file_get_contents($sqlFile);
        unlink($sqlFile);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $writtenSql);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $writtenSql);
    }

    public function testWritingSqlTwiceIntoSameFileDoesNotKeepSqlOfPreviousRun(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();
        $sqlFile = tempnam(sys_get_temp_dir(), 'migration_sql_');
        $executor = $this->get(ConfigurableMigrationExecutorInterface::class);

        $executor->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());
        $executor->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());

        $writtenSql = file_get_contents($sqlFile);
        unlink($sqlFile);
        $this->assertSame(1, substr_count($writtenSql, 'CREATE TABLE `test_migration_table`'));
    }

    public function testWritingSqlIntoDirectoryCreatesOneFileWithMigrationsOfAllProviders(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlDirectory = $this->createTemporaryDirectory();

        $this->get(ConfigurableMigrationExecutorInterface::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlDirectory], new NullOutput());

        $sqlFiles = $this->readSqlFiles($sqlDirectory);
        $this->assertCount(1, $sqlFiles);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $sqlFiles[0]);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $sqlFiles[0]);
    }

    public function testWritingSqlWithoutPathCreatesOneFileInWorkingDirectoryWithMigrationsOfAllProviders(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlDirectory = $this->createTemporaryDirectory();
        $workingDirectory = getcwd();

        chdir($sqlDirectory);
        try {
            $this->get(ConfigurableMigrationExecutorInterface::class)
                ->executeWithOptions(['--dry-run' => true, '--write-sql' => null], new NullOutput());
        } finally {
            chdir($workingDirectory);
        }

        $sqlFiles = $this->readSqlFiles($sqlDirectory);
        $this->assertCount(1, $sqlFiles);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $sqlFiles[0]);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $sqlFiles[0]);
    }

    public function testWritingSqlIntoExistingFileReplacesItsContent(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlFile = tempnam(sys_get_temp_dir(), 'migration_sql_');
        file_put_contents($sqlFile, '-- previous run;');

        $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], new NullOutput());

        $writtenSql = file_get_contents($sqlFile);
        unlink($sqlFile);
        $this->assertStringNotContainsString('-- previous run;', $writtenSql);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $writtenSql);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $writtenSql);
    }

    public function testWritingSqlIntoDirectoryCreatesOneFileWithoutComposite(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlDirectory = $this->createTemporaryDirectory();

        $status = $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlDirectory], new NullOutput());

        $sqlFiles = $this->readSqlFiles($sqlDirectory);
        $this->assertSame(0, $status);
        $this->assertCount(1, $sqlFiles);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $sqlFiles[0]);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $sqlFiles[0]);
    }

    public function testWritingSqlWithoutPathCreatesOneFileWithoutComposite(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlDirectory = $this->createTemporaryDirectory();
        $workingDirectory = getcwd();

        chdir($sqlDirectory);
        try {
            $this->get(TaggedMigrationExecutor::class)
                ->executeWithOptions(['--dry-run' => true, '--write-sql' => null], new NullOutput());
        } finally {
            chdir($workingDirectory);
        }

        $sqlFiles = $this->readSqlFiles($sqlDirectory);
        $this->assertCount(1, $sqlFiles);
        $this->assertStringContainsString('CREATE TABLE `test_migration_table`', $sqlFiles[0]);
        $this->assertStringContainsString('CREATE TABLE `test_second_migration_table`', $sqlFiles[0]);
    }

    public function testReportsFailureWhenSqlCannotBeAppended(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/MultipleSets');
        $this->compileContainer();
        $sqlFile = $this->createTemporaryDirectory() . '/readonly.sql';
        touch($sqlFile);
        chmod($sqlFile, 0444);
        $output = new BufferedOutput();

        $status = $this->get(TaggedMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $sqlFile], $output);

        chmod($sqlFile, 0644);
        $this->assertSame(1, $status);
        $this->assertStringContainsString('Failed to write the migration SQL output file', $output->fetch());
    }

    public function testProvidersAreExecutedInDescendingPriorityOrder(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/Priority');
        $this->compileContainer();
        $executedProviders = [];
        $this->registerProviderStubs(['low', 'high', 'default'], $executedProviders);

        $this->get(TaggedMigrationExecutor::class)->executeWithOptions([], new NullOutput());

        $this->assertSame(['high', 'default', 'low'], $executedProviders);
    }

    protected function tearDown(): void
    {
        $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
        $connection->executeStatement('DROP TABLE IF EXISTS `test_migration_table`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_migrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_failing_migrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_second_migrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_second_migration_table`');
        $this->removeTemporaryDirectories();
        parent::tearDown();
    }

    private function createTemporaryDirectory(): string
    {
        $directory = sys_get_temp_dir() . '/migration_sql_' . uniqid();
        mkdir($directory);
        $this->temporaryDirectories[] = $directory;

        return $directory;
    }

    private function removeTemporaryDirectories(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            array_map('unlink', glob($directory . '/*'));
            rmdir($directory);
        }
    }

    /** @return string[] */
    private function readSqlFiles(string $directory): array
    {
        return array_map('file_get_contents', glob($directory . '/*.sql'));
    }

    private function registerProviderStubs(array $providerIds, array &$executedProviders): void
    {
        foreach ($providerIds as $providerId) {
            $provider = $this->createStub(MigrationPathProviderInterface::class);
            $provider->method('getMigrationConfigPath')->willReturnCallback(
                function () use ($providerId, &$executedProviders): string {
                    $executedProviders[] = $providerId;

                    throw new MigrationsNotFoundException();
                }
            );
            $this->container->set('oxid_esales.tests.migration_path_provider.' . $providerId, $provider);
        }
    }

    private function assertMigrationWasTracked(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('*')->from('test_migrations_tracking');

        $this->assertEquals(1, $queryBuilder->execute()->rowCount());
    }
}
