<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManagerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class ShopDbManagerTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    public function tearDown(): void
    {
        $this->setupShopDatabase();
    }

    public function testCreateOnNonExistingDatabase(): void
    {
        $dbManager = $this->get(ShopDbManagerInterface::class);
        $dbConfig = $this->getDatabaseConfig();
        $this->dropDatabaseIfExists($dbConfig);

        $dbManager->create($dbConfig);

        $this->assertDatabaseIsCreated($dbConfig);
    }

    public function testCreateOnEmptyDatabase(): void
    {
        $dbManager = $this->get(ShopDbManagerInterface::class);
        $databaseConfig = $this->getDatabaseConfig();
        $this->recreateEmptyDatabase($databaseConfig);

        $dbManager->create($databaseConfig);

        $this->assertDatabaseIsCreated($databaseConfig);
    }

    private function getDatabaseConfig(): DatabaseConfiguration
    {
        return new DatabaseConfiguration(getenv('OXID_DB_URL'));
    }

    private function dropDatabaseIfExists(DatabaseConfiguration $config): void
    {
        $this->getDbConnection()->executeStatement("DROP DATABASE IF EXISTS `{$config->getName()}`;");
        $this->getDbConnection()->close();
    }

    private function recreateEmptyDatabase(DatabaseConfiguration $config): void
    {
        $this->getDbConnection()->executeStatement("DROP DATABASE IF EXISTS `{$config->getName()}`;");
        $this->getDbConnection()->executeStatement(
            sprintf('CREATE DATABASE `%s` CHARACTER SET utf8 COLLATE utf8_general_ci;', $config->getName())
        );
        $this->getDbConnection()->close();
    }

    private function assertDatabaseIsCreated(DatabaseConfiguration $config): void
    {
        $connection = $this->get(SetupDbConnectionFactoryInterface::class)->getDatabaseConnection($config);

        $migrationsCount = $connection->fetchOne('SELECT COUNT(*) FROM `oxmigrations_ce`');
        $viewRows = $connection->fetchOne('SELECT COUNT(*) FROM `oxv_oxshops_de`');

        $this->assertGreaterThan(1, $migrationsCount, 'Expected some migrations to be applied.');
        $this->assertGreaterThan(0, $viewRows, 'Expected shop views to contain rows.');
    }
}
