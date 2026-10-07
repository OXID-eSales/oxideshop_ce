<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Command\DatabaseMigrateCommand;
use OxidEsales\EshopCommunity\Tests\ConsoleRunnerTrait;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseMigrateCommandTest extends TestCase
{
    use ConsoleRunnerTrait;
    use ContainerTrait;

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

    protected function tearDown(): void
    {
        try {
            $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
            $connection->executeStatement('DROP TABLE IF EXISTS `test_migration_table`');
            $connection->executeStatement('DROP TABLE IF EXISTS `test_migrations_tracking`');
        } catch (\Throwable) {
        }
        parent::tearDown();
    }

    private function assertMigrationWasTracked(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('COUNT(*)')->from('test_migrations_tracking');

        $this->assertEquals(1, $queryBuilder->executeQuery()->fetchOne());
    }
}
