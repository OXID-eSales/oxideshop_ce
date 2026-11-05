<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Command\DatabaseMigrateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Tests\ConsoleRunnerTrait;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class DatabaseMigrateCommandTest extends TestCase
{
    use ConsoleRunnerTrait;
    use ContainerTrait;

    private string $sqlFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlFile = Path::join(sys_get_temp_dir(), uniqid('migration_', true) . '.sql');
    }

    public function testMigrateOutputsEditionMigrations(): void
    {
        $process = $this->runInConsole('oe:database:migrate --dry-run');

        $output = $process->getOutput() . $process->getErrorOutput();

        $this->assertSame(0, $process->getExitCode());
        $this->assertStringContainsString('OxidEsales\EshopCommunity\Migrations', $output);
    }

    public function testReturnsNonZeroExitCodeWhenMigrationFails(): void
    {
        $process = $this->runInConsole('oe:database:migrate --dry-run --write-sql=/nonexistent-dir/x.sql');

        $this->assertNotSame(0, $process->getExitCode());
    }

    public function testMigrateExecutesTaggedComponentMigrations(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/../Fixtures');
        $this->compileContainer();

        $commandTester = new CommandTester($this->get(DatabaseMigrateCommand::class));

        $this->assertSame(0, $commandTester->execute([]));
        $this->assertMigrationWasTracked();
    }

    public function testWritesSqlOfAllMigrationSetsIntoGivenFile(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/../Fixtures/MultipleSets');
        $this->compileContainer();
        (new Filesystem())->dumpFile($this->sqlFile, '-- previous run');
        $commandTester = new CommandTester($this->get(DatabaseMigrateCommand::class));

        $status = $commandTester->execute(['--dry-run' => true, '--write-sql' => $this->sqlFile]);

        $writtenSql = file_get_contents($this->sqlFile);

        $this->assertSame(0, $status);
        $this->assertStringNotContainsString('-- previous run', $writtenSql);
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE `test_migration_table`.+CREATE TABLE `test_second_migration_table`/s',
            $writtenSql
        );
    }

    public function testMigrateExecutesModuleMigrations(): void
    {
        $this->installModule('myTestModuleWithMigrations');

        $commandTester = new CommandTester($this->get(DatabaseMigrateCommand::class));

        $this->assertSame(0, $commandTester->execute([]));
        $this->assertModuleMigrationWasExecuted();
    }

    protected function tearDown(): void
    {
        $this->uninstallModule('myTestModuleWithMigrations');

        try {
            $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
            $connection->executeStatement('DROP TABLE IF EXISTS `test_migration_table`');
            $connection->executeStatement('DROP TABLE IF EXISTS `test_migrations_tracking`');
            $connection->executeStatement('DROP TABLE IF EXISTS `test_module_migration_table`');
            $connection->executeStatement('DROP TABLE IF EXISTS `test_module_with_migrations`');
            $connection->executeStatement('DROP TABLE IF EXISTS `test_second_migrations_tracking`');
        } catch (\Throwable) {
        }
        (new Filesystem())->remove($this->sqlFile);
        parent::tearDown();
    }

    private function installModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)->install($this->getModulePackage($moduleId));
    }

    private function uninstallModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)->uninstall($this->getModulePackage($moduleId));
    }

    private function getModulePackage(string $moduleId): OxidEshopPackage
    {
        return new OxidEshopPackage(__DIR__ . '/../../Module/Migration/Fixtures/' . $moduleId);
    }

    private function assertModuleMigrationWasExecuted(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('*')->from('test_module_migration_table');

        $this->assertSame(1, $queryBuilder->execute()->rowCount());
    }

    private function assertMigrationWasTracked(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('*')->from('test_migrations_tracking');

        $this->assertEquals(1, $queryBuilder->execute()->rowCount());
    }
}
