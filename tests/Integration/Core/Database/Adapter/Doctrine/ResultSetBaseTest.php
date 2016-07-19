<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\Eshop\Tests\Integration\Core\Database\Adapter\Doctrine;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Tests\Integration\Core\Database\Adapter\DatabaseInterfaceImplementationBaseTest;

/**
 * Tests for our database object.
 *
 * @group database-adapter
 */
abstract class ResultSetBaseTest extends DatabaseInterfaceImplementationBaseTest
{

    /**
     * @var string The name of the class, including the complete namespace.
     */
    const CLASS_NAME_WITH_PATH = 'OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet';

    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'OxidEsales\Eshop\Core\Exception\DatabaseException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\Adapter\Doctrine\ResultSet';

    /**
     * @return string The name of the database exception class
     */
    protected function getDatabaseExceptionClassName()
    {
        return static::DATABASE_EXCEPTION_CLASS;
    }

    /**
     * @return string The name of the result set class
     */
    protected function getResultSetClassName()
    {
        return static::RESULT_SET_CLASS;
    }

    /**
     * Close the database connection.
     */
    protected function closeConnection()
    {
        if (method_exists($this->database, 'closeConnection')) {
            $this->database->closeConnection();
        }
    }

    /**
     * @return array The parameters we want to use for the testFieldCount method.
     */
    public function dataProviderTestFieldCount()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, 1),
            array('SELECT * FROM ' . self::TABLE_NAME, 2)
        );
    }

    /**
     * Test, that the method 'fieldCount' works as expected.
     *
     * @dataProvider dataProviderTestFieldCount
     *
     * @param string $query         The sql statement we want to test.
     * @param int    $expectedCount The expected number of fields.
     */
    public function testFieldCount($query, $expectedCount)
    {
        $resultSet = $this->database->select($query);

        $this->assertSame($expectedCount, $resultSet->fieldCount());
    }


    /**
     * Test, that an empty resultSet leads to zero iterations.
     */
    public function testGetIteratorEmptyResultSet()
    {
        $count = $this->countQueryIterations('SELECT * FROM oxvouchers');

        $this->assertEquals(0, $count);
    }

    /**
     * Test, that a non empty resultSet leads to multiple iterations.
     */
    public function testGetIteratorNonEmptyResultSet()
    {
        $count = $this->countQueryIterations('SELECT * FROM oxarticles');

        $this->assertGreaterThan(200, $count);
    }

    /**
     * @return array The parameters we want to use for the testFields method.
     */
    public function dataProviderTestFields()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, false, false),
            array('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', true, array(self::FIXTURE_OXID_1)),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', true, array('OXID' => self::FIXTURE_OXID_1, 'OXUSERID' => self::FIXTURE_OXUSERID_1), true),
        );
    }

    /**
     * Test, that the method 'fields' works as expected.
     *
     * @dataProvider dataProviderTestFields
     *
     * @param string $query                The sql statement to execute.
     * @param bool   $loadFixture          Should the fixture be loaded to the test database table?
     * @param mixed  $expected             The expected result of the fields method under the given specification.
     * @param bool   $fetchModeAssociative Should the fetch mode be set to associative array before running the statement?
     */
    public function testFields($query, $loadFixture, $expected, $fetchModeAssociative = false)
    {
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
    public function testCreationWithRealEmptyResult()
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
    public function testFetchRowWithEmptyResultSet()
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
    public function testFetchRowWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertSame(array(self::FIXTURE_OXID_2), $methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_3), $resultSet->fields);
        $this->assertSame(array(self::FIXTURE_OXID_3), $methodResult);
    }

    /**
     * Test, that the method 'fetchRow' works for a non empty result set.
     */
    public function testFetchRowWithNonEmptyResultSetReachingEnd()
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
    public function testFetchRowWithNonEmptyResultSetFetchModeAssociative()
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode(Database::FETCH_MODE_ASSOC);
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);
        $this->initializeDatabase();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_2), $methodResult);

        $methodResult = $resultSet->fetchRow();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_3), $resultSet->fields);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_3), $methodResult);
    }

    /**
     * Test, that the method 'fetchAll' works for an empty result set.
     */
    public function testFetchAllWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $rows = $resultSet->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertEmpty($rows);
    }

    /**
     * Test, that the method 'fetchAll' works for a non empty result set.
     */
    public function testFetchAllWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);
        $rows = $resultSet->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertNotEmpty($rows);
        $this->assertSame(3, count($rows));
        $this->assertSame(self::FIXTURE_OXID_1, $rows[0][0]);
        $this->assertSame(self::FIXTURE_OXID_2, $rows[1][0]);
        $this->assertSame(self::FIXTURE_OXID_3, $rows[2][0]);
    }

    /**
     * Test, that the method 'fetchAll' works as expected, if we set first a fetch mode different from the default.
     */
    public function testFetchAllWithDifferentFetchMode()
    {
        $this->loadFixtureToTestTable();
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_BOTH);

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $rows = $resultSet->fetchAll();

        $expectedRows = array(
            array('OXID' => self::FIXTURE_OXID_1, self::FIXTURE_OXID_1),
            array('OXID' => self::FIXTURE_OXID_2, self::FIXTURE_OXID_2),
            array('OXID' => self::FIXTURE_OXID_3, self::FIXTURE_OXID_3)
        );

        $this->assertArrayContentSame($rows, $expectedRows);
    }

    /**
     * Test, that the attribute and method 'EOF' is true, for an empty result set.
     */
    public function testEofWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $this->assertTrue($resultSet->EOF);
    }

    /**
     * Test, that the 'EOF' is true, for a non empty result set.
     */
    public function testEofWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
    }

    /**
     * Test, that the method 'close' works as expected for an empty result set.
     */
    public function testCloseEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $resultSet->close();

        $this->assertTrue($resultSet->EOF);
        $this->assertSame(array(), $resultSet->fields);
    }

    /**
     * Test, that the method 'close' works as expected for an empty result set with fetching a row after closing the cursor.
     */
    public function testCloseEmptyResultSetWithFetchingAfterClosing()
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
    public function testCloseNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $firstRow = $resultSet->getFields();

        $resultSet->close();

        $this->assertSame(array(self::FIXTURE_OXID_1), $firstRow);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(), $resultSet->fields);
    }

    /**
     * Test, that the method 'fetchRow' gives back the correct result, when iterating over it
     */
    public function testGetRowIteration()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $expectedResults = array(
            [self::FIXTURE_OXID_1],
            [self::FIXTURE_OXID_2],
            [self::FIXTURE_OXID_3],
        );


        $this->assertSame($expectedResults[0], $resultSet->getFields());
        $counter = 1;
        while ($row = $resultSet->fetchRow()) {
            $this->assertSame($expectedResults[$counter], $row);
            $counter++;
        };
        $resultSet->close();
    }


    /**
     *
     */
    public function testResultSetFields()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE OXID in (?, ?)',
            array(self::FIXTURE_OXID_2, self::FIXTURE_OXID_3)
        );
        $this->assertSame(
            array(
                0 => 'OXID_2',
                1 => 'OXUSERID_2',
            ),
            $resultSet->fields
        );
    }

    /**
     * Get a resultSet and count the iterations of the iterator.
     *
     * @param string $query The query we want to check, how many iterations it will lead to.
     *
     * @return int The number of iterations the iterator has done.
     */
    protected function countQueryIterations($query)
    {
        $resultSet = $this->database->select($query);

        $count = 0;
        foreach ($resultSet->getIterator() as $row) {
            $count++;
        }

        return $count;
    }

    /**
     * Assert, that the given object is a doctrine result set.
     *
     * @param ResultSet $resultSet The object to check.
     */
    private function assertDoctrineResultSet($resultSet)
    {
        $resultSetClassName = $this->getResultSetClassName();

        $this->assertSame($resultSetClassName, get_class($resultSet));
    }

    /**
     * Assert, that the given arrays have the same content. Useful, if the content is not ordered as expected.
     *
     * @param array $resultArray   The array we got.
     * @param array $expectedArray The array we expect.
     */
    private function assertArrayContentSame($resultArray, $expectedArray)
    {
        $this->assertSame(sort($resultArray), sort($expectedArray));
    }
}
