<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\Doctrine;

use Exception;
use InvalidArgumentException;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\ResultSet;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseErrorException;
use OxidEsales\EshopCommunity\Core\Exception\DatabaseException;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\DatabaseInterfaceImplementation;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use TypeError;

final class DatabaseTest extends DatabaseInterfaceImplementation
{
    public const DATABASE_EXCEPTION_CLASS = DatabaseErrorException::class;

    public const RESULT_SET_CLASS = ResultSet::class;

    protected $database;

    /**
     * Test that the expected exception is thrown for an invalid function parameter.
     * See the data provider for arguments considered invalid.
     *
     * @param mixed $invalidParameter A parameter, which is considered invalid and will trigger an exception
     */
    #[DataProvider('dataProviderTestGetAllThrowsDatabaseExceptionOnInvalidArguments')]
    public function testGetAllThrowsDatabaseExceptionOnInvalidArguments(mixed $invalidParameter): void
    {
        $expectedExceptionClass = '\InvalidArgumentException';
        $this->expectException($expectedExceptionClass);

        $this->database->getAll(
            'SELECT OXID FROM ' . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'",
            $invalidParameter
        );
    }

    /**
     * Test, that the method 'selectLimit' returns the expected rows from the database for different
     * values of limit and offset.
     *
     * This test assumes that there are at least 3 entries in the table.
     *
     * @param string $assertionMessage A message explaining the assertion
     * @param int|string $rowCount Maximum number of rows to return
     * @param string|int $offset Offset of the first row to return
     * @param array $expectedResult The expected result of the method call.
     */
    #[DataProvider('dataProviderTestSelectLimitForInvalidOffsetAndLimit')]
    public function testSelectLimitForInvalidOffsetAndLimit(
        string $assertionMessage,
        int|string $rowCount,
        string|int $offset,
        array $expectedResult
    ): void {
        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
            '"' . self::FIXTURE_OXID_1 . '",' .
            '"' . self::FIXTURE_OXID_2 . '",' .
            '"' . self::FIXTURE_OXID_3 . '"' .
            ')';
        if ($offset < 0) {
            $this->expectException(InvalidArgumentException::class);
        }
        $resultSet = $this->database->selectLimit($sql, $rowCount, $offset);
        $this->assertError(
            'Parameters rowCount and offset have to be numeric in DatabaseInterface::selectLimit(). ' .
            'Please fix your code as this error may trigger an exception in future versions of OXID eShop.'
        );
        $actualResult = $resultSet->fetchAll();

        $this->assertSame($expectedResult, $actualResult, $assertionMessage);
    }

    /**
     * Data provider for testing selectLimit() with invalid parameters
     */
    public static function dataProviderTestSelectLimitForInvalidOffsetAndLimit(): array
    {
        return [[
            'If parameter rowCount is integer 2 and offset is string " UNION SELECT oxusername FROM oxuser" , a warning will be triggered and the first 2 rows will be returned',
            2,
            // row count
            ' UNION SELECT oxusername FROM oxuser',
            // offset
            [
                [self::FIXTURE_OXID_1], [self::FIXTURE_OXID_2],  // expected result
            ],
        ], [
            'If parameter rowCount is integer 2 and offset is string "1  UNION SELECT oxusername FROM oxuser -- " , a warning will be triggered and last 2 rows will be returned',
            2,
            // row count
            '1  UNION SELECT oxusername FROM oxuser',
            // offset
            [
                [self::FIXTURE_OXID_2], [self::FIXTURE_OXID_3],  // expected result
            ],
        ], [
            'If parameter rowCount is string " UNION SELECT oxusername FROM oxuser  --" and offset is 0, a warning will be triggered and the first 2 rows will be returned',
            ' UNION SELECT oxusername FROM oxuser  --',
            // row count
            0,
            // offset
            [],
        ], [
            'If parameter rowCount is string "1 UNION SELECT oxusername FROM oxuser  --" and offset is 0, a warning will be triggered and the first 2 rows will be returned',
            '1  UNION SELECT oxusername FROM oxuser --',
            // row count
            0,
            // offset
            [
                [self::FIXTURE_OXID_1],  // expected result
            ],
        ]];
    }

    /**
     * Verify that method 'selectLimit' does not allow a negative offset value.
     */
    public function testSelectLimitForOffsetBelowZero(): void
    {
        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
            '"' . self::FIXTURE_OXID_1 . '",' .
            '"' . self::FIXTURE_OXID_2 . '",' .
            '"' . self::FIXTURE_OXID_3 . '"' .
            ')';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument $offset must not be smaller than zero.');

        $this->database->selectLimit($sql, 1, -1);
    }

    public function testSetTransactionIsolationLevelThrowsExpectedExceptionOnInvalidParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->database->setTransactionIsolationLevel('INVALID TRANSACTION ISOLATION LEVEL');
    }

    public function testExceptionGetCodeAndExceptionGetMessageReturnSameResultsAsErrorNoAndErrorMsg(): void
    {
        $expectedCode = self::EXPECTED_MYSQL_SYNTAX_ERROR_CODE;

        try {
            $this->database->execute('INVALID SQL QUERY');
            $actualCode = 0;
        } catch (Exception $exception) {
            $actualCode = $exception->getCode();
        }

        $this->assertSame($expectedCode, $actualCode);
    }

    public function testQuoteIdentifierWithValidValues(): void
    {
        $this->loadFixtureToTestTable();
        $quotedIdentifier = $this->database->quoteIdentifier('OXID');

        $expectedResult = [[self::FIXTURE_OXID_1]];
        $resultSet = $this->database
            ->select(
                'SELECT OXID FROM ' . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "' ORDER BY " . $quotedIdentifier
            );
        $actualResult = $resultSet->fetchAll();

        $this->assertSame($expectedResult, $actualResult);
    }

    #[DataProvider('dataProviderTestQuoteIdentifierWithInvalidValues')]
    public function testQuoteIdentifierWithInvalidValues(string $identifier, string $expectedMessage): void
    {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage($expectedMessage);

        $quotedIdentifier = $this->database->quoteIdentifier($identifier);

        $this->database->select('SELECT * FROM ' . self::TABLE_NAME . ' ORDER BY ' . $quotedIdentifier);
    }

    public static function dataProviderTestQuoteIdentifierWithInvalidValues(): array
    {
        return [
            [
                // A arbitrary string will be converted in a column name
                'SELECT * from oxuser',
                'Unknown column \'SELECT * from oxuser\' in \'order clause\'',
            ],
            [
                // A arbitrary string, which contains a backtick, will be converted in a column name
                'columnName ` columnName',
                'Unknown column \'columnName  columnName\' in \'order clause\'',
            ],
        ];
    }

    #[DataProvider('dataProviderTestQuoteWithInvalidValues')]
    public function testQuoteWithInvalidValues(
        mixed $value,
        mixed $expectedQuotedValue,
        string $expectedException,
        string $message
    ): void {
        $this->loadFixtureToTestTable();

        $this->expectException(TypeError::class);
        $actualQuotedValue = $this->database->quote($value);
        $this->assertSame($expectedQuotedValue, $actualQuotedValue, $message);

        $this->expectException($expectedException);

        $query = 'SELECT OXID FROM ' . self::TABLE_NAME . " WHERE OXID = {$actualQuotedValue}";
        $resultSet = $this->database->select($query);
        $resultSet->fetchAll();
    }

    public function testExceptionForDuplicatedEntry(): void
    {
        $tableName = self::TABLE_NAME;
        $id = self::FIXTURE_OXID_1;
        $this->database->execute('ALTER TABLE `oxdoctrinetest`ADD UNIQUE `oxid` (`oxid`);');
        $this->database->execute("INSERT INTO {$tableName} (OXID) VALUES ('{$id}');");

        try {
            $this->database->execute("INSERT INTO {$tableName} (OXID) VALUES ('{$id}');");
        } catch (DatabaseErrorException $e) {
            $this->assertEquals(DatabaseInterface::DUPLICATE_KEY_ERROR_CODE, $e->getCode());
            return;
        }

        $this->fail('Database exception must be thrown due to duplicated entry.');
    }

    public static function dataProviderTestQuoteWithInvalidValues(): array
    {
        return [
            [[
                'key' => 'value',
            ],
                false,
                self::DATABASE_EXCEPTION_CLASS,
                'An array will be converted into boolean "false" and an exception is thrown, when the statement is executed '
            ],
            [
                new stdClass(),
                false,
                self::DATABASE_EXCEPTION_CLASS,
                'An object will be converted into boolean "false" and an exception is thrown, when the statement is executed',
            ],
        ];
    }

    /**
     * Test, that affected rows is set to the expected values by consecutive calls to execute()
     */
    public function testExecuteSetsAffectedRows(): void
    {
        $this->loadFixtureToTestTable();

        /** One row will be updated by the query */
        $expectedAffectedRows = 1;
        $actualAffectedRows = $this->database->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET oxuserid = "somevalue" WHERE OXID = ?',
            [self::FIXTURE_OXID_1]
        );

        $this->assertEquals($expectedAffectedRows, $actualAffectedRows, '1 row was updated by the query');

        /** Two rows will be updated by the query */
        $expectedAffectedRows = 2;
        $actualAffectedRows = $this->database->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET oxuserid = "someothervalue" WHERE OXID IN (?, ?)',
            [self::FIXTURE_OXID_1, self::FIXTURE_OXID_2]
        );

        $this->assertEquals($expectedAffectedRows, $actualAffectedRows, '2 rows was updated by the query');
    }

    /**
     * Test, that the method 'execute' works for insert and delete.
     */
    public function testExecuteWithInsertAndDelete(): void
    {
        $this->truncateTestTable();

        $exampleOxId = self::FIXTURE_OXID_1;

        $affectedRows = $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('{$exampleOxId}');"
        );

        $this->assertSame(1, $affectedRows);
        $this->assertTestTableHasOnly($exampleOxId);

        $affectedRows = $this->database->execute('DELETE FROM ' . self::TABLE_NAME . " WHERE OXID = '{$exampleOxId}';");

        $this->assertSame(1, $affectedRows);
        $this->assertTestTableIsEmpty();
    }

    /**
     * Test, that the method 'getRow' gives an empty array with empty table and default fetch mode.
     */
    public function testGetRowEmptyTableDefaultFetchMode(): void
    {
        $result = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test, that the method 'getAll' leads to unique rows with the SQL clause 'ORDER BY rand()'.
     */
    public function testGetAllWithOrderByRand(): void
    {
        $resultSet = $this->database->select('SELECT oxid FROM oxarticles ORDER BY RAND()');
        $rows = $resultSet->fetchAll();
        $oxIds = [];

        foreach ($rows as $row) {
            $oxIds[] = $row[0];
        }

        $this->assertArrayIsUnique($oxIds);
    }

    /**
     * Test, that the method 'moveNext' leads to unique rows with the SQL clause 'ORDER BY rand()'.
     */
    public function testMoveNextWithOrderByRand(): void
    {
        $resultSet = $this->database->select('SELECT oxid FROM oxarticles ORDER BY RAND()');
        $oxIds = [];

        while (!$resultSet->EOF) {
            $oxIds[] = $resultSet->fields[0];
            $resultSet->fetchRow();
        }

        $this->assertArrayIsUnique($oxIds);
    }

    /*
     * After applying the driverOptions to the Doctrine DriverManager, in our case the sql_mode should be
     * set on the database connection.
     */
    public function testAddDriverOptionsSetsSqlMode(): void
    {
        $query = 'SELECT @@SESSION.sql_mode';

        $expectedSqlMode = '';
        $actualSqlMode = $this->database->getOne($query);
        $this->assertSame(
            $expectedSqlMode,
            $actualSqlMode,
            'The sql_mode variable on the database is not the expected one.'
        );
    }

    protected function createDatabase()
    {
        return DatabaseProvider::getMaster();
    }

    protected function closeConnection(): void
    {
        $this->database->closeConnection();
    }

    protected function getDatabaseExceptionClassName(): string
    {
        return self::DATABASE_EXCEPTION_CLASS;
    }

    protected function getResultSetClassName(): string
    {
        return self::RESULT_SET_CLASS;
    }

    /**
     * Assert a given error level and a given error message
     *
     * @param string $errorMessage Error message
     */
    private function assertError(string $errorMessage): void
    {
        foreach ($this->errors as $error) {
            if (
                $error['errorMessage'] === $errorMessage
                && $error['errorLevel'] === E_USER_DEPRECATED
            ) {
                return;
            }
        }

        $this->fail('No error with level ' . E_USER_DEPRECATED . " and message '" . $errorMessage . "' was triggered");
    }

    private function assertArrayIsUnique(array $expectUnique): void
    {
        $unique = array_unique($expectUnique);
        $this->assertEquals($unique, $expectUnique, 'There should not be any doubled entries in the given array!');
    }
}
