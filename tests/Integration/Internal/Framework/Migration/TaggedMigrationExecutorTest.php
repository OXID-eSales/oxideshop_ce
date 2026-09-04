<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\TaggedMigrationExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class TaggedMigrationExecutorTest extends TestCase
{
    use ContainerTrait;

    public function testMigrationIsExecuted(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->compileContainer();

        $status = $this->get(TaggedMigrationExecutor::class)->executeWithOptions();

        $this->assertSame(0, $status);
        $this->assertMigrationWasTracked();
    }

    #[DoesNotPerformAssertions]
    public function testNoErrorWhenConfigFileDoesNotExist(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/NoConfig');
        $this->compileContainer();

        $this->get(TaggedMigrationExecutor::class)->executeWithOptions();
    }

    public function testReturnsZeroWhenProviderHasNoMigrations(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/NoMigrations');
        $this->compileContainer();

        $this->assertSame(0, $this->get(TaggedMigrationExecutor::class)->executeWithOptions());
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

        $executor->executeWithOptions([]);
        $this->assertTrue($connection->getSchemaManager()->tablesExist(['test_migration_table']));

        $connection->executeStatement('DROP TABLE test_migration_table');
        $connection->executeStatement('DROP TABLE test_migrations_tracking');

        $executor->executeWithOptions(['--dry-run' => true]);

        $this->assertFalse($connection->getSchemaManager()->tablesExist(['test_migration_table']));
    }

    public function testProvidersExecuteInDescendingPriorityOrder(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures/Priority');
        $this->compileContainer();

        $this->get(TaggedMigrationExecutor::class)->executeWithOptions();

        $this->assertSame(['high', 'medium', 'low'], $this->getPriorityExecutionOrder());
    }

    protected function tearDown(): void
    {
        $connection = $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
        $connection->executeStatement('DROP TABLE IF EXISTS `test_migration_table`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_migrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_nomigrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_failing_migrations_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_priority_order_log`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_priority_high_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_priority_medium_tracking`');
        $connection->executeStatement('DROP TABLE IF EXISTS `test_priority_low_tracking`');
        parent::tearDown();
    }

    /**
     * @return string[]
     */
    private function getPriorityExecutionOrder(): array
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('source')->from('test_priority_order_log')->orderBy('id', 'ASC');

        return $queryBuilder->execute()->fetchFirstColumn();
    }

    private function assertMigrationWasTracked(): void
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->select('*')->from('test_migrations_tracking');

        $this->assertEquals(1, $queryBuilder->execute()->rowCount());
    }
}
