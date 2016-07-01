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

namespace OxidEsales\Eshop\Tests\integration\core\Database\Adapter;

use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Doctrine;
use OxidEsales\Eshop\Tests\Integration\Core\Database\DatabaseInterfaceImplementationBaseTest;

/**
 * Tests for our database object.
 *
 * @group database-adapter
 */
abstract class ResultSetTest extends DatabaseInterfaceImplementationBaseTest
{

    /**
     * @var string The name of the class, including the complete namespace.
     */
    const CLASS_NAME_WITH_PATH = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'OxidEsales\Eshop\Core\Exception\DatabaseException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet';

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
     * @return string The name of the empty result set class
     */
    protected function getEmptyResultSetClassName()
    {
        return static::EMPTY_RESULT_SET_CLASS;
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
     * Test, that the method 'moveNext' works for an empty result set.
     */
    public function testMoveNextWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'moveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->moveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->moveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_3), $resultSet->fields);
        $this->assertTrue($methodResult);
    }

    /**
     * Test, that the method 'moveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSetReachingEnd()
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $resultSet->moveNext();
        $resultSet->moveNext();
        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);

        $methodResult = $resultSet->moveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'moveNext' works for a non empty result set and the fetch mode associative array.
     */
    public function testMoveNextWithNonEmptyResultSetFetchModeAssociative()
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode(Doctrine::FETCH_MODE_ASSOC);
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);
        $this->initializeDatabase();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->moveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->moveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_3), $resultSet->fields);
        $this->assertTrue($methodResult);
    }

    /**
     * @return array The parameters we want to use for the testGetRows and testGetArray methods.
     */
    public function dataProviderTestGetRowsTestGetArray()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, 0, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 1, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 10, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 0, true, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 1, true, array(array(self::FIXTURE_OXID_1))),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 5, true, array(array(self::FIXTURE_OXID_1), array(self::FIXTURE_OXID_2), array(self::FIXTURE_OXID_3))),
        );
    }

    /**
     * Test, that the method 'getArray' works as expected.
     *
     * @dataProvider dataProviderTestGetRowsTestGetArray
     *
     * @param string $query         The sql statement we want to execute.
     * @param int    $numberOfRows  The number of rows we want to fetch.
     * @param bool   $loadFixtures  Should we load the test fixtures before running the actual test.
     * @param array  $expectedArray The result the method should give back.
     */
    public function testGetArray($query, $numberOfRows, $loadFixtures, $expectedArray)
    {
        if ($loadFixtures) {
            $this->loadFixtureToTestTable();
        }

        $resultSet = $this->database->select($query);

        $result = $resultSet->getArray($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'getArray' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetArraySequentialCalls()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->getArray(1);
        $resultTwo = $resultSet->getArray(1);
        $resultThree = $resultSet->getArray(1);

        $this->assertSame($resultOne, array(array(self::FIXTURE_OXID_1)));
        $this->assertSame($resultTwo, array(array(self::FIXTURE_OXID_2)));
        $this->assertSame($resultThree, array(array(self::FIXTURE_OXID_3)));
    }

    /**
     * Test, that the method 'getArray' works as expected, if we set first a fetch mode different from the default.
     */
    public function testGetArrayWithDifferentFetchMode()
    {
        $this->loadFixtureToTestTable();
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_BOTH);

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->getArray(1);
        $resultTwo = $resultSet->getArray(1);
        $resultThree = $resultSet->getArray(1);

        $expectedOne = array(array('OXID' => self::FIXTURE_OXID_1, self::FIXTURE_OXID_1));
        $expectedTwo = array(array('OXID' => self::FIXTURE_OXID_2, self::FIXTURE_OXID_2));
        $expectedThree = array(array('OXID' => self::FIXTURE_OXID_3, self::FIXTURE_OXID_3));

        $this->assertArrayContentSame($resultOne, $expectedOne);
        $this->assertArrayContentSame($resultTwo, $expectedTwo);
        $this->assertArrayContentSame($resultThree, $expectedThree);
    }

    /**
     * Test, that the method 'getRows' works as expected.
     *
     * @dataProvider dataProviderTestGetRowsTestGetArray
     *
     * @param string $query         The sql statement to execute.
     * @param int    $numberOfRows  The number of rows to fetch.
     * @param bool   $loadFixtures  Should we load the test fixtures before running the actual test.
     * @param array  $expectedArray The resulting array, which we expect.
     */
    public function testGetRows($query, $numberOfRows, $loadFixtures, $expectedArray)
    {
        if ($loadFixtures) {
            $this->loadFixtureToTestTable();
        }

        $resultSet = $this->database->select($query);

        $result = $resultSet->getRows($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'getRows' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetRowsSequentialCalls()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->getRows(1);
        $resultTwo = $resultSet->getRows(1);
        $resultThree = $resultSet->getRows(1);

        $this->assertSame($resultOne, array(array(self::FIXTURE_OXID_1)));
        $this->assertSame($resultTwo, array(array(self::FIXTURE_OXID_2)));
        $this->assertSame($resultThree, array(array(self::FIXTURE_OXID_3)));
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
     * @return array The parameters we want to use for the testFields method.
     */
    public function dataProviderTestFields()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, 0, false, false),
            array('SELECT OXID FROM ' . self::TABLE_NAME, 'OXID', false, null),
            array('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array(self::FIXTURE_OXID_1)),
            array('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'OXID', true, null),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1)),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 1, true, self::FIXTURE_OXUSERID_1),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'OXID', true, self::FIXTURE_OXID_1, true),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 0, true, array('OXID' => self::FIXTURE_OXID_1, 'OXUSERID' => self::FIXTURE_OXUSERID_1), true),
            array('SELECT OXID,OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID', 'NOTNULL', true, null, true),
        );
    }

    /**
     * Test, that the method 'fields' works as expected.
     *
     * @dataProvider dataProviderTestFields
     *
     * @param string $query                The sql statement to execute.
     * @param mixed  $parameter            The parameter for the fields method.
     * @param bool   $loadFixture          Should the fixture be loaded to the test database table?
     * @param mixed  $expected             The expected result of the fields method under the given specification.
     * @param bool   $fetchModeAssociative Should the fetch mode be set to associative array before running the statement?
     */
    public function testFields($query, $parameter, $loadFixture, $expected, $fetchModeAssociative = false)
    {
        if ($loadFixture) {
            $this->loadFixtureToTestTable();
        }
        if ($fetchModeAssociative) {
            $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);
        }

        $resultSet = $this->database->select($query);
        $result = $resultSet->fields($parameter);

        $this->truncateTestTable();
        $this->assertSame($expected, $result);
    }

    /**
     * Test, that the result set of an empty select works as expected.
     *
     * @return DoctrineResultSet The empty result set.
     */
    public function testCreationWithRealEmptyResult()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertDoctrineResultSet($resultSet);
        $this->assertSame(0, $resultSet->recordCount());

        return $resultSet;
    }

    /**
     * Test, that the result set of a non empty select works as expected.
     *
     * @return DoctrineResultSet The non empty result set.
     */
    public function testCreationWithRealNonEmptyResult()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertDoctrineResultSet($resultSet);
        $this->assertSame(3, $resultSet->recordCount());

        return $resultSet;
    }

    /**
     * Test, that the method 'fetchRow' works for an empty result set.
     */
    public function testFetchRowWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $row = $resultSet->fetchRow();

        $this->assertFalse($row);
    }

    /**
     * Test, that the method 'fetchRow' works for a non empty result set.
     */
    public function testFetchRowWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $row = $resultSet->fetchRow();

        $this->assertInternalType('array', $row);
        // You can get the first row with getFields() method. The fetchRow() method will take the next record.
        $this->assertSame(self::FIXTURE_OXID_2, $row[0]);
    }

    /**
     * Test, that the method 'getAll' works for an empty result set.
     */
    public function testGetAllWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $rows = $resultSet->getAll();

        $this->assertInternalType('array', $rows);
        $this->assertEmpty($rows);
    }

    /**
     * Test, that the method 'getAll' works for a non empty result set.
     */
    public function testGetAllWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $rows = $resultSet->getAll();

        $this->assertInternalType('array', $rows);
        $this->assertNotEmpty($rows);
        $this->assertSame(3, count($rows));
        $this->assertSame(self::FIXTURE_OXID_1, $rows[0][0]);
    }

    /**
     * Test, that the attribute and method 'EOF' is true, for an empty result set.
     */
    public function testEofWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $this->assertTrue($resultSet->EOF);
        $this->assertTrue($resultSet->EOF());
    }

    /**
     * Test, that the 'EOF' is true, for a non empty result set.
     */
    public function testEofWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
        $this->assertFalse($resultSet->EOF());
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
     * Assert, that the given object is a doctrine result set.
     *
     * @param DoctrineResultSet $resultSet The object to check.
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
