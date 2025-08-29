<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter;

use Doctrine\DBAL\TransactionIsolationLevel;
use InvalidArgumentException;
use oxDb;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

abstract class DatabaseInterfaceImplementation extends DatabaseInterfaceImplementationBase
{
    public function testSelectWithParameters(): void
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select(
            'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID = ?', [self::FIXTURE_OXID_2]
        );

        $result = $resultSet->fetchAll();

        $this->assertEquals([['OXID' => self::FIXTURE_OXID_2]], $result);
    }

    public function testSelectWithNonReadStatementThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->database->select('INSERT INTO ' . self::TABLE_NAME . ' VALUES (\'a\',\'b\')');
    }

    public function testSelectPreparedWithInvalidParameterDoesNotThrowException(): void
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select(
            'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID = ?', [[ 'key' => 'value']]
        );

        $result = $resultSet->fetchAll();

        $this->assertEquals([], $result);
    }

    public function testSelectLimitWithParameters(): void
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select(
            'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID <> ?', [self::FIXTURE_OXID_2]
        );

        $result = $resultSet->fetchAll();

        $this->assertEquals([['OXID' => self::FIXTURE_OXID_1], ['OXID' => self::FIXTURE_OXID_3]], $result);
    }

    public static function dataProviderTestSelectLimitForDifferentLimitAndOffsetValues(): array
    {
        return [[
            'If parameter rowCount is integer 0, no rows are returned at all',
            0,
            // row count
            0,
            // offset
            [],
        ], [
            'If parameter rowCount is string "2" and offset is string "0", the first 2 rows will be returned',
            '2',
            // row count as a string
            '0',
            // offset as string
            [
                ['OXID' => self::FIXTURE_OXID_1], ['OXID' => self::FIXTURE_OXID_2],  // expected result
            ],
        ], [
            'If parameter rowCount has the value 2 and offset has the value 0, the first 2 rows will be returned',
            2,
            // row count
            0,
            // offset
            [
                ['OXID' => self::FIXTURE_OXID_1], ['OXID' => self::FIXTURE_OXID_2],  // expected result
            ],
        ], [
            'If parameter rowCount has the value 2 and offset has the value 1, the last 2 rows will be returned',
            2,
            // row count
            1,
            // offset
            [
                ['OXID' => self::FIXTURE_OXID_2], ['OXID' => self::FIXTURE_OXID_3], // expected result
            ],
        ]];
    }

    #[DataProvider('dataProviderTestSelectLimitForDifferentLimitAndOffsetValues')]
    public function testSelectLimitReturnsExpectedResultForDifferentOffsetAndLimit(
        string $assertionMessage,
        int|string $rowCount,
        int|string $offset,
        array $expectedResult
    ): void {
        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
               '"' . self::FIXTURE_OXID_1 . '",' .
               '"' . self::FIXTURE_OXID_2 . '",' .
               '"' . self::FIXTURE_OXID_3 . '"' .
               ')';

        $resultSet = $this->database->selectLimit($sql, $rowCount, $offset);
        $actualResult = $resultSet->fetchAll();

        $this->assertSame($expectedResult, $actualResult, $assertionMessage);
    }

    public function testSelectLimitReturnsExpectedResultForMissingOffsetParameter(): void
    {
        $rowCount = 2;
        $expectedResult = [['OXID' => self::FIXTURE_OXID_1], ['OXID' => self::FIXTURE_OXID_2]];
        $assertionMessage = 'If parameter offet is not set, selectLimit will return the number of records 
        given in the parameter $rowcount starting from the first record in the result set';

        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
               '"' . self::FIXTURE_OXID_1 . '",' .
               '"' . self::FIXTURE_OXID_2 . '",' .
               '"' . self::FIXTURE_OXID_3 . '"' .
               ')';

        $resultSet = $this->database->selectLimit($sql, $rowCount);
        $actualResult = $resultSet->fetchAll();

        $this->assertSame($expectedResult, $actualResult, $assertionMessage);
    }

    public function testSelectWithEmptyResultSelect(): void
    {
        $result = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $expectedRows = [];
        $allRows = $result->fetchAll();
        $this->assertSame($expectedRows, $allRows);
    }

    public function testExecuteWithEmptyResultAndSelectNotOnFirstChar(): void
    {
        $result = $this->database->select('   SELECT OXID FROM ' . self::TABLE_NAME);

        $expectedRows = [];
        $allRows = $result->fetchAll();
        $this->assertSame($expectedRows, $allRows);
    }

    public function testExecuteWithNonEmptySelect(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $this->assertFalse($result->EOF);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_1], $result->fields);

        $expectedRows = [
            ['OXID' => self::FIXTURE_OXID_1],
            ['OXID' => self::FIXTURE_OXID_2],
            ['OXID' => self::FIXTURE_OXID_3]
        ];
        $allRows = $result->fetchAll();

        $this->assertSame($expectedRows, $allRows);
    }

    public function testExecuteThrowsExceptionForInvalidNonSelectQueryString(): void
    {
        $this->expectException(DatabaseErrorException::class);

        $this->database->execute('SOME INVALID QUERY');
    }

    public function testSelectThrowsExceptionForInvalidSelectQueryString(): void
    {
        $this->expectException(DatabaseErrorException::class);

        oxDb::getMaster()->select('SELECT SOME INVALID QUERY', []);
    }

    public function testSetTransactionIsolationLevel(): void
    {
        $connection = $this->database->getPublicConnection();

        $transactionIsolationLevelPre = $connection->getTransactionIsolation();

        $expectedLevel = TransactionIsolationLevel::READ_COMMITTED;
        $this->database->setTransactionIsolationLevel('READ COMMITTED');
        $transactionIsolationLevel = $connection->getTransactionIsolation();

        $this->assertSame($expectedLevel, $transactionIsolationLevel);

        $connection->setTransactionIsolation($transactionIsolationLevelPre);
    }

    public function testGetColWithoutParametersEmptyResult(): void
    {
        $result = $this->database->getCol('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertIsArray($result);
        $this->assertSame(0, count($result));
    }

    public function testGetColWithoutParameters(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getCol('SELECT OXUSERID FROM ' . self::TABLE_NAME);

        $this->assertIsArray($result);
        $this->assertSame(3, count($result));
        $this->assertSame([self::FIXTURE_OXUSERID_1, self::FIXTURE_OXUSERID_2, self::FIXTURE_OXUSERID_3], $result);
    }

    public function testGetColWithParameters(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getCol(
            'SELECT OXUSERID FROM ' . self::TABLE_NAME . ' WHERE OXUSERID LIKE ? ',
            ['%2']
        );

        $this->assertIsArray($result);
        $this->assertSame(1, count($result));
        $this->assertSame([self::FIXTURE_OXUSERID_2], $result);
    }

    public function testGetColhWithNonReadStatementThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->database->getCol('INSERT INTO ' . self::TABLE_NAME . " VALUES ('a', 'b')");
    }

    public function testRollbackTransactionRevertsChanges(): void
    {
        $exampleOxId = 'XYZ';

        $this->truncateTestTable();
        $this->database->startTransaction();
        $this->database->execute('INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('{$exampleOxId}');", []);

        // assure, that the changes are made in this transaction
        $this->assertTestTableHasOnly($exampleOxId);

        $this->database->rollbackTransaction();

        // assure, that the changes are reverted
        $this->assureTestTableIsEmpty();
    }

    public function testCommitTransactionCommitsChanges(): void
    {
        $exampleOxId = 'XYZ';

        $this->truncateTestTable();
        $this->database->startTransaction();
        $this->database->execute('INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('{$exampleOxId}');", []);

        // assure, that the changes are made in this transaction
        $this->assertTestTableHasOnly($exampleOxId);
        $this->database->commitTransaction();

        // assure, that the changes persist the transaction
        $this->assertTestTableHasOnly($exampleOxId);
    }

    public function testGetAllWithEmptyParameter(): void
    {
        $message = 'The expected result is returned when passing an empty array as parameter to Doctrine::getAll()';
        $expectedResult = [['OXID' => self::FIXTURE_OXID_1]];

        $this->truncateTestTable();
        $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')"
        );

        $actualResult = $this->database->getAll(
            'SELECT OXID FROM ' . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'"
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    public function testGetAllWithOneParameter(): void
    {
        $message = 'The expected result is returned when passing an array with one parameter to Doctrine::getAll()';
        $expectedResult = [['OXID' => self::FIXTURE_OXID_1]];

        $this->truncateTestTable();
        $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')"
        );

        $actualResult = $this->database->getAll(
            'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID = ?',
            [self::FIXTURE_OXID_1]
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    public function testGetAllWithMoreThanOneParameters(): void
    {
        $message = 'The expected result is returned when passing an array with more than one parameter'
            . ' to Doctrine::getAll()';
        $expectedResult = [
            [
                'OXID' => self::FIXTURE_OXID_1
            ],
            [
                'OXID' => self::FIXTURE_OXID_2
            ]
        ];

        $this->truncateTestTable();
        $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')"
        );
        $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_2 . "')"
        );

        $actualResult = $this->database->getAll(
            'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (?, ?)',
            [self::FIXTURE_OXID_1, self::FIXTURE_OXID_2]
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    public function testGetAllThrowsDatabaseExceptionOnInvalidQueryString(): void
    {
        $this->expectException(DatabaseErrorException::class);

        $this->database->getAll('SOME INVALID QUERY', []);
    }

    public function testInsertIdOnNonAutoIncrement(): void
    {
        $this->database->execute(
            'INSERT INTO ' . self::TABLE_NAME . ' (OXUSERID) VALUES ("' . self::FIXTURE_OXUSERID_1 . '")'
        );

        $this->expectException(DatabaseErrorException::class);

        $this->database->getLastInsertId();
    }

    public function testInsertIdWithoutInsertion(): void
    {
        $this->database->select('SELECT * FROM ' . self::TABLE_NAME);

        $this->expectException(DatabaseErrorException::class);

        $this->database->getLastInsertId();
    }

    public function testInsertIdWithInsertion(): void
    {
        $this->database->execute(
            'CREATE TABLE oxdoctrinetest_autoincrement '
            . '(oxid INT NOT NULL AUTO_INCREMENT, oxname CHAR, PRIMARY KEY (oxid));'
        );

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $firstInsertedId = $this->database->getLastInsertId();

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $lastInsertedId = $this->database->getLastInsertId();

        $this->database->execute('DROP TABLE oxdoctrinetest_autoincrement;');

        $this->assertEquals(1, $firstInsertedId);
        $this->assertEquals(2, $lastInsertedId);
    }

    public function testGetOneWithEmptyTable(): void
    {
        $result = $this->database->getOne('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertFalse($result);
    }

    public function testGetOneWithWrongSqlStatement(): void
    {
        $result = $this->database->getOne(
            'INSERT INTO ' . self::TABLE_NAME . " (oxid) VALUES ('" . self::FIXTURE_OXID_1 . "')"
        );

        $this->assertFalse($result);
    }

    public function testGetOneWithNonEmptyTable(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertEquals(self::FIXTURE_OXID_1, $result);
    }

    public function testGetOneWithShowStatement(): void
    {
        $result = $this->database->getOne('SHOW COLUMNS FROM ' . self::TABLE_NAME);

        $this->assertEquals('oxid', $result);
    }

    public function testGetOneWithNonEmptyTableAndGivenColumnName(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT OXUSERID FROM ' . self::TABLE_NAME);

        $this->assertEquals(self::FIXTURE_OXUSERID_1, $result);
    }

    public function testGetOneWithEmptyParameters(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT OXUSERID FROM ' . self::TABLE_NAME, []);

        $this->assertEquals(self::FIXTURE_OXUSERID_1, $result);
    }

    public function testGetOneWithNonEmptyParameters(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne(
            'SELECT OXUSERID FROM ' . self::TABLE_NAME . ' WHERE oxid = ?',
            [self::FIXTURE_OXID_3]
        );

        $this->assertEquals(self::FIXTURE_OXUSERID_3, $result);
    }

    public function testGetRowIncorrectSqlStatement(): void
    {
        $this->truncateTestTable();

        /**
         * An exception will be logged as part of the BC layer, when calling the getRow with a wrong SQL statement
         * The exception log will be cleared at the end of this test
         */
        $this->expectException(InvalidArgumentException::class);
        $result = $this->database->getRow(
            'INSERT INTO ' . self::TABLE_NAME . " (oxid) VALUES ('" . self::FIXTURE_OXID_1 . "')"
        );

        $this->assertIsArray($result);
        $this->assertEmpty($result);

        $expectedExceptionClass = DatabaseErrorException::class;
        $this->assertLoggedException($expectedExceptionClass);
    }

    public function testGetRowNonEmptyTableWithParameters(): void
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getRow(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE oxid = ?',
            [self::FIXTURE_OXID_2]
        );

        $this->assertIsArray($result);
        $this->assertEquals(['oxid' => self::FIXTURE_OXID_2, 'oxuserid' => self::FIXTURE_OXUSERID_2], $result);
    }

    /**
     * Test, that the method 'getRow' gives back the correct result, when called with parameters and consecutive calls.
     */
    public function testGetRowNonEmptyTableWithParametersAndConsecutiveCalls(): void
    {
        $this->loadFixtureToTestTable();

        $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);
        $result = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertIsArray($result);
        $this->assertEquals(['oxid' => self::FIXTURE_OXID_1, 'oxuserid' => self::FIXTURE_OXUSERID_1], $result);
    }

    public function testMetaColumnsMethod(): void
    {
        $metaColumnsTestTable = self::TABLE_NAME . '_testmetacolumns';
        $this->createTableForTestMetaColumns($metaColumnsTestTable);
        $columnInformation = $this->database->metaColumns($metaColumnsTestTable);

        $expectedColumns = $this->getExpectedColumnsByTestMetaColumns();

        foreach ($expectedColumns as $key => $sub) {
            foreach ($sub as $attributeName => $attributeValue) {
                $this->assertObjectHasAttributeWithValue($columnInformation[$key], $attributeName, $attributeValue);
            }
        }
    }

    #[DataProvider('dataProviderTestQuoteWithValidValues')]
    public function testQuoteWithValidValues(
        mixed $value,
        mixed $expectedQuotedValue,
        mixed $expectedResult,
        string $message
    ): void {
        $this->loadFixtureToTestTable();

        $actualQuotedValue = $this->database->quote($value);

        $this->assertSame($expectedQuotedValue, $actualQuotedValue, $message);

        $query = 'SELECT OXID FROM ' . self::TABLE_NAME . " WHERE OXID = {$actualQuotedValue}";
        $resultSet = $this->database->select($query);
        $actualResult = $resultSet->fetchAll();

        $this->assertSame($expectedResult, $actualResult, $message);
    }

    public static function dataProviderTestQuoteWithValidValues(): array
    {
        return [
            [
                self::FIXTURE_OXID_1,
                "'" . self::FIXTURE_OXID_1 . "'",
                [['OXID' => self::FIXTURE_OXID_1]],
                'The string "' . self::FIXTURE_OXID_1 . '" 1  will be converted into the string "\'' .
                self::FIXTURE_OXID_1 . '\'" and the query result will be [' . self::FIXTURE_OXID_1 . ']',
            ],
            [
                1,
                "'1'",
                [],
                'The integer 1  will be converted into the string "1" and the query result will be empty'
            ],
            [
                1.5,
                "'1.5'",
                [],
                'The float 1.5 will be converted into the string "1.5" and the query result will be empty',
            ],
            [
                false,
                "''",
                [],
                'The boolean false will be converted into the empty string and the query result will be empty',
            ],
            [
                true,
                "'1'",
                [],
                'The boolean true will be converted into the string "1" and the query result will be empty',
            ],
            [
                null,
                "''",
                [],
                'The null value will be converted into the empty string and the query result will be empty',
            ],
        ];
    }

    public static function resetDbProperty($class): void
    {
        $reflectionClass = new ReflectionClass(DatabaseProvider::class);

        $reflectionProperty = $reflectionClass->getProperty('db');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, null);
    }

    /*
     * There is a another special table needed for testMetaColumns.
     *
     * @param string $metaColumnsTestTable The name of the table to create
     */
    protected function createTableForTestMetaColumns(string $metaColumnsTestTable)
    {
        $dbh = self::getDatabaseHandler();
        $dbh->exec('CREATE TABLE IF NOT EXISTS ' . $metaColumnsTestTable . " (
            OXINT INT(11) NOT NULL AUTO_INCREMENT COMMENT 'a column with type INT',
            OXUSERID CHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'  COMMENT 'a column with type CHAR',
            OXTIME TIME COMMENT 'a column of type TIME',
            OXBIT BIT(6) NOT NULL  COMMENT 'a column with type BIT',
            OXDEC DEC(6,2) UNSIGNED NOT NULL DEFAULT 1.3 COMMENT 'a column with type DECIMAL',
            OXTEXT TEXT  CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL COMMENT 'a column with type TEXT',
            OXID CHAR(32)  CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL COMMENT 'a column with type CHAR',
            OXBLOB BLOB  COMMENT 'a column with type BLOB',
            OXFLOAT FLOAT(5,2) UNSIGNED NOT NULL DEFAULT 1.3 COMMENT 'a column with type FLOAT',
            PRIMARY KEY (OXINT)
        ) ENGINE innoDb;");
    }

    /*
     * Specify which results the method 'metaColumns' expects for each column of the testing table.
     */
    protected function getExpectedColumnsByTestMetaColumns(): array
    {
        return [
            'OXINT' => [
                'name' => 'OXINT',
                'type' => 'int',
                'not_null' => true,
                'primary_key' => true,
                'auto_increment' => true,
                'binary' => false,
                'unsigned' => false,
                'has_default' => false,
                'comment' => 'a column with type INT',
            ],
            'OXUSERID' => [
                'name' => 'OXUSERID',
                'max_length' => '32',
                'type' => 'char',
                'not_null' => false,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => false,
                'comment' => 'a column with type CHAR',
            ],
            'OXTIME' => [
                'name' => 'OXTIME',
                'type' => 'time',
                'not_null' => false,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => false,
                'comment' => 'a column of type TIME',
            ],
            'OXBIT' => [
                'name' => 'OXBIT',
                'max_length' => '6',
                'type' => 'bit',
                'not_null' => true,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => false,
                'comment' => 'a column with type BIT',
            ],
            'OXDEC' => [
                'name' => 'OXDEC',
                'max_length' => '6',
                'type' => 'decimal',
                'not_null' => true,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => true,
                'has_default' => true,
                'default_value' => '1.30',
                'scale' => '2',
                'comment' => 'a column with type DECIMAL',
            ],
            'OXTEXT' => [
                'name' => 'OXTEXT',
                'type' => 'text',
                'not_null' => true,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => false,
                'has_default' => false,
                'comment' => 'a column with type TEXT',
            ],
            'OXID' => [
                'name' => 'OXID',
                'max_length' => '32',
                'type' => 'char',
                'not_null' => true,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => false,
                'has_default' => false,
                'comment' => 'a column with type CHAR',
            ],
            'OXBLOB' => [
                'name' => 'OXBLOB',
                'type' => 'blob',
                'not_null' => false,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => true,
                'unsigned' => false,
                'comment' => 'a column with type BLOB',
            ],
            'OXFLOAT' => [
                'name' => 'OXFLOAT',
                'max_length' => '5',
                'scale' => '2',
                'type' => 'float',
                'not_null' => true,
                'primary_key' => false,
                'auto_increment' => false,
                'binary' => false,
                'unsigned' => true,
                'has_default' => true,
                'default_value' => '1.30',
            ]
        ];
    }

    /**
     * Assure, that the table oxdoctrinetest has only the given oxId.
     *
     * @param string $oxId The oxId we want to be the only one in the oxdoctrinetest table.
     */
    protected function assertTestTableHasOnly($oxId)
    {
        $oxIds = $this->fetchAllTestTableRows();

        $this->assertNotEmpty($oxIds);
        $this->assertSame(1, count($oxIds));
        $this->assertArrayHasKey('0', $oxIds);

        $this->assertSame($oxId, $oxIds[0]['oxid']);
    }

    /**
     * Assert, that the table oxdoctrinetest is empty.
     */
    protected function assertTestTableIsEmpty()
    {
        $this->assertTrue($this->isEmptyTestTable());
    }

    /**
     * Fetch the oxId of the first oxdoctrinetest table row.
     *
     * @return array|false The oxId of the first oxdoctrinetest table row.
     */
    protected function fetchFirstTestTableOxId()
    {
        $masterDb = oxDb::getMaster();

        $rows = $masterDb->select('SELECT OXID FROM ' . self::TABLE_NAME, []);

        return $rows->fetchRow();
    }

    /**
     * Check, if the table oxdoctrinetest is empty.
     *
     * @return bool Is the table oxdoctrinetest empty?
     */
    protected function isEmptyTestTable(): bool
    {
        return empty($this->fetchAllTestTableRows());
    }
}
