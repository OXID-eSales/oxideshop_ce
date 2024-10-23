<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Migration;

use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

/**
 * Class ModuleMigrationsTest
 */
class ModuleMigrationsTest extends TestCase
{
    use ContainerTrait;

    private $moduleIdWithMigrations= 'myTestModuleWithMigrations';
    private $moduleIdWithoutMigrations = 'myTestModuleWithoutMigrations';

    public function testMigrationsExecutionWithSpecificModule(): void
    {
        $this->installModule($this->moduleIdWithMigrations);

        $migrations = $this->getMigrations();
        $migrations->execute(Migrations::MIGRATE_COMMAND, $this->moduleIdWithMigrations);

        $this->assertIfMigrationExistsInDatabase();

        $this->removeTestModule($this->moduleIdWithMigrations);
    }

    #[DoesNotPerformAssertions]
    public function testNoErrorWhenModuleHasNoMigrations(): void
    {
        $this->installModule($this->moduleIdWithoutMigrations);

        $migrations = $this->getMigrations();
        $migrations->execute(Migrations::MIGRATE_COMMAND, 'myTestModule');

        $this->removeTestModule($this->moduleIdWithoutMigrations);
    }

    public function testAllMigrationsExecuteHasModuleMigrationInside(): void
    {
        $this->installModule($this->moduleIdWithMigrations);

        $migrations = $this->getMigrations();
        $migrations->execute(Migrations::MIGRATE_COMMAND);

        $this->assertIfMigrationExistsInDatabase();

        $this->removeTestModule($this->moduleIdWithMigrations);
    }

    /**
     * @param string $moduleId
     */
    private function installModule(string $moduleId): void
    {
        $package = new OxidEshopPackage(__DIR__ . '/Fixtures/' . $moduleId);
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->install($package);
    }

    /**
     * @param string $moduleId
     */
    private function removeTestModule(string $moduleId): void
    {
        $package = new OxidEshopPackage(__DIR__ . '/Fixtures/' . $moduleId);
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->uninstall($package);
    }

    private function assertIfMigrationExistsInDatabase(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('test_module_with_migrations');

        $this->assertEquals(1, $queryBuilder->execute()->rowCount());
    }

    private function getMigrations(): Migrations
    {
        $migrations = (new MigrationsBuilder())->build();

        $output = new ConsoleOutput();
        $output->setVerbosity(ConsoleOutputInterface::VERBOSITY_QUIET);
        $migrations->setOutput($output);

        return $migrations;
    }
}
