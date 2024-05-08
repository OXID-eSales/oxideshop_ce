<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\Doctrine;

use oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet;
use OxidEsales\Eshop\Core\Exception\DatabaseException;
use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\DatabaseInterfaceImplementationBase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ResultSetTest extends DatabaseInterfaceImplementationBase
{
    /**
     * @return array The parameters we want to use for the testFieldCount method.
     */
    public static function dataProviderTestFieldCount(): array
    {
        return [['SELECT OXID FROM ' . self::TABLE_NAME, 1], ['SELECT * FROM ' . self::TABLE_NAME, 2]];
    }

    /**
     * Test, that the method 'fieldCount' works as expected.
     *
     * @param string $query         The sql statement we want to test.
     * @param int    $expectedCount The expected number of fields.
     */
    #[DataProvider('dataProviderTestFieldCount')]
    public function testFieldCount(string $query, int $expectedCount): void
    {
        $resultSet = $this->database->select($query);

        $this->assertSame($expectedCount, $resultSet->fieldCount());
    }

    /**
     * Test, that an empty resultSet leads to zero iterations.
     */
    public function testGetIteratorEmptyResultSet(): void
    {
        $nonExistingId = uniqid('some-id-', true);
        $count = $this->countQueryIterations("SELECT * FROM oxconfig where oxid = '{$nonExistingId}'");

        $this->assertEquals(0, $count);
    }

    /**
     * Test, that a non empty resultSet leads to multiple iterations.
     */
    public function testGetIteratorNonEmptyResultSet(): void
    {
        $count = $this->countQueryIterations('SELECT * FROM oxconfig');

        $this->assertGreaterThan(0, $count);
    }

    /**
     * @return array The parameters we want to use for the testFields method.
     */
    public static function dataProviderTestFields(): array
    {
        return [['SELECT OXID FROM ' . self::TABLE_NAME, false, false], [
            'SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID',
            true,
            [self::FIXTURE_OXID_1]], ['SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', true, [
                'OXID' => self::FIXTURE_OXID_1,
                'OXUSERID' => self::FIXTURE_OXUSERID_1,
            ], true]];
    }

    /**
     * Test, that the method 'fields' works as expected.
     *
     * @param string $query                The sql statement to execute.
     * @param bool   $loadFixture          Should the fixture be loaded to the test database table?
     * @param mixed  $expected             The expected result of the fields method under the given specification.
     * @param bool   $fetchModeAssociative Should the fetch mode be set to associative array before running the statement?
     */
    #[DataProvider('dataProviderTestFields')]
    public function testFields(
        string $query,
        bool $loadFixture,
        mixed $expected,
        bool $fetchModeAssociative = false
    ): void {
        if ($loadFixture) {
            $this->loadFixtureToTestTable();
        }
        if ($fetchModeAssociative) {
            $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);
        }

        $resultSet = $this->database->select($query);

        $this->truncateTestTable();
        $this->assertSame($expected, $resultSet->getFields());
    }

    /**
     * Test, that the result set of an empty select works as expected.
     *
     * @return ResultSet The empty result set.
     */
    public function testCreationWithRealEmptyResult(): ResultSet
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertDoctrineResultSet($resultSet);
        $this->assertSame(0, $resultSet->count());

        return $resultSet;
    }

    /**
     * Test, that the result set of a non empty select works as expected.
     *
     * @return ResultSet The non empty result set.
     */
    public function testCreationWithRealNonEmptyResult()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertDoctrineResultSet($resultSet);
        $this->assertSame(3, $resultSet->count());

        return $resultSet;
    }

    /**
     * Test, that the method 'fetchRow' works for an empty result set.
     */
    public function testFetchRowWithEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $methodResult = $resultSet->fetchRow();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->getFields());
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'fetchRow' works for a non empty result set.
     */
    public function testFetchRowWithNonEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([self::FIXTURE_OXID_1], $resultSet->fields);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([self::FIXTURE_OXID_2], $resultSet->fields);
        $this->assertSame([self::FIXTURE_OXID_2], $methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([self::FIXTURE_OXID_3], $resultSet->fields);
        $this->assertSame([self::FIXTURE_OXID_3], $methodResult);
    }

    /**
     * Test, that the method 'fetchRow' works for a non empty result set.
     */
    public function testFetchRowWithNonEmptyResultSetReachingEnd(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $resultSet->fetchRow();
        $resultSet->fetchRow();
        $methodResult = $resultSet->fetchRow();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'fetchRow' works for a non empty result set and the fetch mode associative array.
     */
    public function testFetchRowWithNonEmptyResultSetFetchModeAssociative(): void
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode(oxDb::FETCH_MODE_ASSOC);
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);
        $this->initializeDatabase();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([
            'OXID' => self::FIXTURE_OXID_1,
        ], $resultSet->fields);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([
            'OXID' => self::FIXTURE_OXID_2,
        ], $resultSet->fields);
        $this->assertSame([
            'OXID' => self::FIXTURE_OXID_2,
        ], $methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame([
            'OXID' => self::FIXTURE_OXID_3,
        ], $resultSet->fields);
        $this->assertSame([
            'OXID' => self::FIXTURE_OXID_3,
        ], $methodResult);
    }

    /**
     * Test, that the method 'fetchAll' works for an empty result set.
     */
    public function testFetchAllWithEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $rows = $resultSet->fetchAll();

        $this->assertIsArray($rows);
        $this->assertEmpty($rows);
    }

    /**
     * Test, that the method 'fetchAll' works for a non empty result set.
     */
    public function testFetchAllWithNonEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertSame([self::FIXTURE_OXID_1], $resultSet->fields);
        $rows = $resultSet->fetchAll();

        $this->assertIsArray($rows);
        $this->assertNotEmpty($rows);
        $this->assertSame(3, count($rows));
        $this->assertSame(self::FIXTURE_OXID_1, $rows[0][0]);
        $this->assertSame(self::FIXTURE_OXID_2, $rows[1][0]);
        $this->assertSame(self::FIXTURE_OXID_3, $rows[2][0]);
    }

    /**
     * Test, that the method 'fetchAll' works as expected, if we set first a fetch mode different from the default.
     */
    public function testFetchAllWithDifferentFetchMode(): void
    {
        $this->loadFixtureToTestTable();
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_BOTH);

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $rows = $resultSet->fetchAll();

        $expectedRows = [[
            'OXID' => self::FIXTURE_OXID_1,
            self::FIXTURE_OXID_1,
        ], [
            'OXID' => self::FIXTURE_OXID_2,
            self::FIXTURE_OXID_2,
        ], [
            'OXID' => self::FIXTURE_OXID_3,
            self::FIXTURE_OXID_3,
        ]];

        $this->assertArrayContentSame($rows, $expectedRows);
    }

    /**
     * Test, that the attribute and method 'EOF' is true, for an empty result set.
     */
    public function testEofWithEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $this->assertTrue($resultSet->EOF);
    }

    /**
     * Test, that the 'EOF' is true, for a non empty result set.
     */
    public function testEofWithNonEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
    }

    /**
     * Test, that the method 'close' works as expected for an empty result set.
     */
    public function testCloseEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $resultSet->close();

        $this->assertTrue($resultSet->EOF);
        $this->assertSame([], $resultSet->fields);
    }

    /**
     * Test, that the method 'close' works as expected for an empty result set with fetching a row after closing the cursor.
     */
    public function testCloseEmptyResultSetWithFetchingAfterClosing(): void
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $resultSet->close();

        $firstRow = $resultSet->fetchRow();

        $this->assertFalse($firstRow);
        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
    }

    /**
     * Test, that the method 'close' works as expected for a non empty result set.
     */
    public function testCloseNonEmptyResultSet(): void
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $firstRow = $resultSet->getFields();

        $resultSet->close();

        $this->assertSame([self::FIXTURE_OXID_1], $firstRow);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame([], $resultSet->fields);
    }

    /**
     * Test, that the method 'fetchRow' gives back the correct result, when iterating over it
     */
    public function testGetRowIteration(): void
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $expectedResults = [[self::FIXTURE_OXID_1], [self::FIXTURE_OXID_2], [self::FIXTURE_OXID_3]];

        $this->assertSame($expectedResults[0], $resultSet->getFields());
        $counter = 1;
        while ($row = $resultSet->fetchRow()) {
            $this->assertSame($expectedResults[$counter], $row);
            $counter++;
        }
        $resultSet->close();
    }

    public function testResultSetFields(): void
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE OXID in (?, ?)',
            [self::FIXTURE_OXID_2, self::FIXTURE_OXID_3]
        );
        $this->assertSame([
            0 => 'OXID_2',
            1 => 'OXUSERID_2',
        ], $resultSet->fields);
    }

    protected function getDatabaseExceptionClassName(): string
    {
        return DatabaseException::class;
    }

    protected function getResultSetClassName(): string
    {
        return ResultSet::class;
    }

    protected function closeConnection(): void
    {
        if (method_exists($this->database, 'closeConnection')) {
            $this->database->closeConnection();
        }
    }

    /**
     * Get a resultSet and count the iterations of the iterator.
     *
     * @param string $query The query we want to check, how many iterations it will lead to.
     *
     * @return int The number of iterations the iterator has done.
     */
    private function countQueryIterations(string $query): int
    {
        $resultSet = $this->database->select($query);

        $count = 0;
        foreach ($resultSet->getIterator() as $row) {
            $count++;
        }

        return $count;
    }

    /**
     * Create the database object under test.
     *
     * @return Doctrine The database object under test.
     */
    protected function createDatabase()
    {
        return oxDb::getMaster();
    }

    /**
     * Assert, that the given object is a doctrine result set.
     *
     * @param ResultSet $resultSet The object to check.
     */
    private function assertDoctrineResultSet($resultSet): void
    {
        $resultSetClassName = $this->getResultSetClassName();

        $this->assertSame($resultSetClassName, $resultSet::class);
    }

    /**
     * Assert, that the given arrays have the same content. Useful, if the content is not ordered as expected.
     *
     * @param array $resultArray   The array we got.
     * @param array $expectedArray The array we expect.
     */
    private function assertArrayContentSame($resultArray, array $expectedArray): void
    {
        $this->assertSame(sort($resultArray), sort($expectedArray));
    }
}
