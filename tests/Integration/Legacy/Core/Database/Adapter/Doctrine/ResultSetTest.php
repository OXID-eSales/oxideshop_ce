<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\Doctrine;

use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\EshopCommunity\Core\Database\Adapter\ResultSetInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Database\Adapter\DatabaseInterfaceImplementationBase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ResultSetTest extends DatabaseInterfaceImplementationBase
{
    public function testFieldCount(): void
    {
        $this->assertEquals(1, $this->getResultSet()->fieldCount());
        $this->assertEquals(2, $this->database->select('SELECT * FROM ' . self::TABLE_NAME)->fieldCount());
    }

    public function testGetIteratorEmptyResultSet(): void
    {
        $nonExistingId = uniqid('some-id-', true);
        $count = $this->countQueryIterations("SELECT * FROM oxconfig where oxid = '{$nonExistingId}'");

        $this->assertEquals(0, $count);
    }

    public function testGetIteratorNonEmptyResultSet(): void
    {
        $count = $this->countQueryIterations('SELECT * FROM oxconfig');

        $this->assertGreaterThan(0, $count);
    }

    public function testFields(): void
    {
        $expected = [
            'OXID' => self::FIXTURE_OXID_1,
            'OXUSERID' => self::FIXTURE_OXUSERID_1,
        ];
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $this->truncateTestTable();
        $this->assertSame($expected, $resultSet->getFields());
    }

    public function testFetchRowWithEmptyResultSet(): void
    {
        $resultSet = $this->getResultSet();
        $this->assertEquals(0, $resultSet->count());

        $methodResult = $resultSet->fetchRow();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->getFields());
        $this->assertFalse($methodResult);
    }

    public function testFetchRowWithNonEmptyResultSet(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->getResultSet();
        $this->assertSame(3, $resultSet->count());

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_1], $resultSet->fields);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_2], $resultSet->fields);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_2], $methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_3], $resultSet->fields);
        $this->assertSame(['OXID' => self::FIXTURE_OXID_3], $methodResult);
    }

    public function testFetchRowWithNonEmptyResultSetReachingEnd(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->getResultSet();

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

    public function testFetchRowWithNonEmptyResult(): void
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->getResultSet();
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

    public function testFetchAllWithEmptyResultSet(): void
    {
        $resultSet = $this->getResultSet();

        $rows = $resultSet->fetchAll();

        $this->assertIsArray($rows);
        $this->assertEmpty($rows);
    }

    public function testFetchAllWithNonEmptyResultSet(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->getResultSet();

        $this->assertSame(['OXID' => self::FIXTURE_OXID_1], $resultSet->fields);
        $rows = $resultSet->fetchAll();

        $this->assertIsArray($rows);
        $this->assertNotEmpty($rows);
        $this->assertSame(3, count($rows));
        $this->assertSame(self::FIXTURE_OXID_1, $rows[0]['OXID']);
        $this->assertSame(self::FIXTURE_OXID_2, $rows[1]['OXID']);
        $this->assertSame(self::FIXTURE_OXID_3, $rows[2]['OXID']);
    }

    public function testEofWithEmptyResultSet(): void
    {
        $this->assertTrue($this->getResultSet()->EOF);
    }

    public function testEofWithNonEmptyResultSet(): void
    {
        $this->loadFixtureToTestTable();

        $this->assertFalse($this->getResultSet()->EOF);
    }

    public function testCloseEmptyResultSet(): void
    {
        $resultSet = $this->getResultSet();

        $resultSet->close();

        $this->assertTrue($resultSet->EOF);
        $this->assertSame([], $resultSet->fields);
    }

    public function testCloseEmptyResultSetWithFetchingAfterClosing(): void
    {
        $resultSet = $this->getResultSet();

        $resultSet->close();

        $firstRow = $resultSet->fetchRow();

        $this->assertFalse($firstRow);
        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
    }

    public function testCloseNonEmptyResultSet(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->getResultSet();

        $firstRow = $resultSet->getFields();

        $resultSet->close();

        $this->assertSame(['OXID' => self::FIXTURE_OXID_1], $firstRow);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame([], $resultSet->fields);
    }

    public function testGetRowIteration(): void
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->getResultSet();

        $expectedResults = [
            ['OXID' => self::FIXTURE_OXID_1],
            ['OXID' => self::FIXTURE_OXID_2],
            ['OXID' => self::FIXTURE_OXID_3]
        ];

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
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE OXID in (?, ?)', [self::FIXTURE_OXID_2, self::FIXTURE_OXID_3]
        );
        $this->assertSame([
            'oxid' => 'OXID_2',
            'oxuserid' => 'OXUSERID_2',
        ], $resultSet->fields);
    }

    private function countQueryIterations(string $query): int
    {
        $resultSet = $this->database->select($query);

        $count = 0;
        foreach ($resultSet->getIterator() as $ignored) {
            $count++;
        }

        return $count;
    }

    private function getResultSet(): ResultSetInterface
    {
        return $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);
    }
}
