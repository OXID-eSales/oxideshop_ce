<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCreator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PDO;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class DatabaseCreatorTest extends TestCase
{
    use ContainerTrait;

    private string $testDatabase = 'oxid_setup_command_db_test';
    private DatabaseConfiguration $dbConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->dropDatabase();
    }

    #[DoesNotPerformAssertions]
    public function testCreateDatabase(): void
    {
        (new DatabaseCreator())
            ->createDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                $this->dbConfig->getUser(),
                $this->dbConfig->getPass(),
                $this->testDatabase
            );
    }

    public function testCreateDatabaseWhenDatabaseCredentialsIsIncorrect(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        (new DatabaseCreator())
            ->createDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                '',
                '',
                $this->testDatabase
            );
    }

    public function testCreateDatabaseWhenDatabaseIsAlreadyExist(): void
    {
        (new DatabaseCreator())
            ->createDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                $this->dbConfig->getUser(),
                $this->dbConfig->getPass(),
                $this->testDatabase
            );

        $this->expectException(DatabaseExistsException::class);
        (new DatabaseCreator())
            ->createDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                $this->dbConfig->getUser(),
                $this->dbConfig->getPass(),
                $this->testDatabase
            );
    }

    private function dropDatabase(): void
    {
        (new PDO(
            "mysql:host={$this->dbConfig->getHost()};port={$this->dbConfig->getPort()}",
            $this->dbConfig->getUser(),
            $this->dbConfig->getPass(),
        ))
            ->exec("DROP SCHEMA IF EXISTS $this->testDatabase");
    }
}
