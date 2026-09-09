<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Migration\ModuleMigrationExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class ModuleMigrationExecutorTest extends TestCase
{
    use ContainerTrait;

    protected function tearDown(): void
    {
        $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
        $connection->executeStatement('DROP TABLE IF EXISTS `test_module_migration_table`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_module_with_migrations`');

        parent::tearDown();
    }

    public function testExecutesMigrationsOfInstalledModule(): void
    {
        $this->installModule('myTestModuleWithMigrations');

        $status = $this->get(ModuleMigrationExecutor::class)->executeWithOptions([], new NullOutput());

        $this->assertSame(0, $status);
        $this->assertSame(1, $this->countRows('test_module_migration_table'));
    }

    public function testForwardsOptionsToModuleMigrations(): void
    {
        $this->installModule('myTestModuleWithMigrations');

        $status = $this->get(ModuleMigrationExecutor::class)
            ->executeWithOptions(['--dry-run' => true], new NullOutput());

        $this->assertSame(0, $status);
        $this->assertFalse($this->tableExists('test_module_migration_table'));
    }

    public function testReturnsSuccessWhenModuleHasNoMigrationDirectory(): void
    {
        $this->installModule('myTestModuleWithoutMigrations');

        $status = $this->get(ModuleMigrationExecutor::class)->executeWithOptions([], new NullOutput());

        $this->assertSame(0, $status);
    }

    private function installModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)->install(new OxidEshopPackage(__DIR__ . '/Fixtures/' . $moduleId));
    }

    private function countRows(string $table): int
    {
        return $this->get(QueryBuilderFactoryInterface::class)->create()
            ->select('*')
            ->from($table)
            ->execute()
            ->rowCount();
    }

    private function tableExists(string $table): bool
    {
        return $this->get(QueryBuilderFactoryInterface::class)->create()
            ->getConnection()
            ->getSchemaManager()
            ->tablesExist([$table]);
    }
}
