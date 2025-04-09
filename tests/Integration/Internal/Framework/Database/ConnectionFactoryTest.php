<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ConnectionFactoryTest extends TestCase
{
    use ContainerTrait;

    private string $logFile = __DIR__ . DIRECTORY_SEPARATOR . 'some-log.log';
    private string $id;
    private string $variable;
    private string $value;

    public function setUp(): void
    {
        parent::setUp();

        (new FileSystem())->touch($this->logFile);
        $this->id = Registry::getUtilsObject()->generateUId();
        $this->variable = 'some-variable';
        $this->value = uniqid('some-value-', true);
    }

    public function tearDown(): void
    {
        (new FileSystem())->remove($this->logFile);

        parent::tearDown();
    }

    public function testSqlLoggerWillWriteNothingForNonAdmin(): void
    {
        $this->executeInsertStatement();

        $this->assertEmpty(file_get_contents($this->logFile));
    }

    public function testSqlLoggerWillWriteNothingForAdminWithConfigDisabled(): void
    {
        $this->setAdminWithLoggingDisabled();

        $this->executeInsertStatement();

        $this->assertEmpty(file_get_contents($this->logFile));
    }

    public function testSqlLoggerWillWriteQueryData(): void
    {
        $this->setAdminWithLoggingEnabled();

        $this->executeInsertStatement();

        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('INSERT INTO', $log);
        $this->assertStringContainsString($this->id, $log);
        $this->assertStringContainsString($this->variable, $log);
        $this->assertStringContainsString($this->value, $log);
    }

    public function testSqlLoggerWillWriteExtendedContextData(): void
    {
        $this->setAdminWithLoggingEnabled();

        $this->executeInsertStatement();

        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('adminUserId', $log);
        $this->assertStringContainsString('shopId', $log);
        $this->assertStringContainsString((new \ReflectionClass(self::class))->getShortName(), $log);
        $this->assertStringContainsString('class', $log);
        $this->assertStringContainsString('function', $log);
        $this->assertStringContainsString('file', $log);
        $this->assertStringContainsString('line', $log);
    }

    public function testSqlLoggerWillWriteNothingForNonLoggedQuery(): void
    {
        $this->setAdminWithLoggingEnabled();

        $this->executeSelectQuery();

        $this->assertEmpty(file_get_contents($this->logFile));
    }

    public function testSqlLoggerWillWriteNothingIfNoQueryExecuted(): void
    {
        $this->setAdminWithLoggingEnabled();

        $this->executeNoQuery();

        $this->assertEmpty(file_get_contents($this->logFile));
    }

    public function testSqlLoggerWillWriteNothingIfQueryIsFiltered(): void
    {
        $this->setAdminWithLoggingEnabled();

        $this->executeQueryWithFilteredWord();

        $this->assertEmpty(file_get_contents($this->logFile));
    }

    private function executeInsertStatement(): void
    {
        $statement = $this
            ->get(ConnectionFactoryInterface::class)
            ->create()
            ->prepare(
                'INSERT INTO `oxconfig` (`OXID`, `OXVARNAME`, `OXVARVALUE`)  VALUES (?, ?, ?);'
            );
        $statement->bindValue(1, $this->id);
        $statement->bindValue(2, $this->variable);
        $statement->bindValue(3, $this->value);

        $statement->executeStatement();
    }

    private function executeSelectQuery(): void
    {
        $this
            ->get(ConnectionFactoryInterface::class)
            ->create()
            ->executeQuery(
                'SELECT *  FROM `oxconfig` LIMIT 1;'
            );
    }

    private function executeNoQuery(): void
    {
        $connection = $this
            ->get(ConnectionFactoryInterface::class)
            ->create();
        $connection->beginTransaction();
        $connection->rollBack();
    }

    private function executeQueryWithFilteredWord(): void
    {
        $statement = $this
            ->get(ConnectionFactoryInterface::class)
            ->create()
            ->prepare(
                'UPDATE `oxconfig` SET `OXVARVALUE` = "123"  WHERE `OXVARNAME` = "oxsession";'
            );

        $statement->executeStatement();
    }

    private function setAdminWithLoggingDisabled(): void
    {
        $this->createContainer();
        $this->stubAdminContext();
        $this->container->setParameter('oxid_esales.log_admin_queries', false);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();
    }

    private function stubAdminContext(): void
    {
        $this->replaceService(
            ContextInterface::class,
            $this->createConfiguredMock(
                ContextInterface::class,
                [
                    'isAdmin' => true,
                    'getAdminLogFilePath' => $this->logFile
                ]
            )
        );
    }

    private function setAdminWithLoggingEnabled(): void
    {
        $this->createContainer();
        $this->stubAdminContext();
        $this->container->setParameter('oxid_esales.log_admin_queries', true);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();
    }
}
