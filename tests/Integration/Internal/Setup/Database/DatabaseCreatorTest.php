<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Exception;
use Throwable;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCreator;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseConnectionException;
use OxidEsales\Facts\Config\ConfigFile;
use PDO;
use PHPUnit\Framework\TestCase;

final class DatabaseCreatorTest extends TestCase
{
    private DatabaseCreator $databaseCreator;

    /** @var array */
    private $params = [];

    public function setUp(): void
    {
        $this->params = $this->getDatabaseConnectionInfo();
        $this->params['dbName'] = 'oxid_setup_command_db_test';

        $this->databaseCreator = new DatabaseCreator();

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->dropDatabase();

        parent::tearDown();
    }

    #[DoesNotPerformAssertions]
    public function testCreateDatabase(): void
    {
        $this->databaseCreator->createDatabase(
            $this->params['dbHost'],
            $this->params['dbPort'],
            $this->params['dbUser'],
            $this->params['dbPwd'],
            $this->params['dbName']
        );
    }

    public function testCreateDatabaseWhenDatabaseCredentialsIsIncorrect(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $this->databaseCreator->createDatabase(
            $this->params['dbHost'],
            $this->params['dbPort'],
            '',
            '',
            $this->params['dbName']
        );
    }

    public function testCreateDatabaseWhenDatabaseIsAlreadyExist(): void
    {
        $this->databaseCreator->createDatabase(
            $this->params['dbHost'],
            $this->params['dbPort'],
            $this->params['dbUser'],
            $this->params['dbPwd'],
            $this->params['dbName']
        );

        $this->expectException(DatabaseExistsException::class);
        $this->databaseCreator->createDatabase(
            $this->params['dbHost'],
            $this->params['dbPort'],
            $this->params['dbUser'],
            $this->params['dbPwd'],
            $this->params['dbName']
        );
    }

    /**
     * @throws Exception
     */
    private function dropDatabase(): void
    {
        try {
            $dbConnection = new PDO(
                sprintf('mysql:host=%s;port=%s', $this->params['dbHost'], $this->params['dbPort']),
                $this->params['dbUser'],
                $this->params['dbPwd'],
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
            );
            $dbConnection->exec('DROP SCHEMA IF EXISTS ' . $this->params['dbName']);
        } catch (Throwable $exception) {
            throw new Exception('Failed: Could not drop database', $exception->getCode(), $exception);
        }
    }

    private function getDatabaseConnectionInfo(): array
    {
        $configFile = new ConfigFile();

        return [
            'dbHost' => $configFile->getVar('dbHost'),
            'dbPort' => (int) $configFile->getVar('dbPort'),
            'dbUser' => $configFile->getVar('dbUser'),
            'dbPwd'  => $configFile->getVar('dbPwd')
        ];
    }
}
