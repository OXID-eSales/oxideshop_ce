<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use Doctrine\DBAL\Exception\DriverException;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManagerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class ShopDbManagerTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    public function testCreate(): void
    {
        $shopDbManager = $this->get(ShopDbManagerInterface::class);
        $dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));
        $this->getDbConnection()->executeStatement(
            "DROP DATABASE `{$dbConfig->getName()}`;"
        );
        $this->getDbConnection()->close();

        $shopDbManager->create($dbConfig);

        $dbConnection = $this->get(SetupDbConnectionFactoryInterface::class)
            ->getDatabaseConnection(
                $dbConfig
            );
        $migrationsCount = $dbConnection->fetchAllAssociative('SELECT COUNT(*) FROM `oxmigrations_ce`');
        $this->assertGreaterThan(1, $migrationsCount);
        $viewRows = $dbConnection->executeQuery('SELECT COUNT(*) FROM `oxv_oxshops_de`');
        $this->assertGreaterThan(0, $viewRows);
    }

    public function testCreateWithExistingDatabase(): void
    {
        $this->expectException(DriverException::class);

        $this->get(ShopDbManagerInterface::class)->create(
            new DatabaseConfiguration(
                getenv('OXID_DB_URL')
            )
        );
    }
}
