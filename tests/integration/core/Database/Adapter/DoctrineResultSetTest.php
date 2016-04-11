<?php
namespace OxidEsales\Eshop\Tests\integration\core\Database\Adapter;

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

use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\Database\Doctrine;
use OxidEsales\Eshop\Tests\integration\core\Database\DatabaseInterfaceImplementationBaseTest;

/**
 * Tests for our database object.
 *
 * @group doctrine
 */
class DoctrineResultSetTest extends DatabaseInterfaceImplementationBaseTest
{

    /**
     * @var string The name of the class, including the complete namespace.
     */
    const CLASS_NAME_WITH_PATH = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The database exception class to be thrown
     */
    const DATABASE_EXCEPTION_CLASS = 'OxidEsales\Eshop\Core\exception\DatabaseException';

    /**
     * @var string The result set class class
     */
    const RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The empty result set class class
     */
    const EMPTY_RESULT_SET_CLASS = 'OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet';

    /**
     * @var bool Use the legacy database adapter.
     *
     * @todo get rid of this
     */
    const USE_LEGACY_DATABASE = false;
    
    /**
     * @return string The name of the database exception class
     */
    protected function getDatabaseExceptionClassName()
    {
        return self::DATABASE_EXCEPTION_CLASS;
    }

    /**
     * @return string The name of the result set class
     */
    protected function getResultSetClassName()
    {
        return self::RESULT_SET_CLASS;
    }

    /**
     * @return string The name of the empty result set class
     */
    protected function getEmptyResultSetClassName()
    {
        return self::EMPTY_RESULT_SET_CLASS;
    }

    /**
     * Set up before beginning with tests
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::createDatabaseTable();
    }

    /**
     * Tear down after all tests are done
     */
    public static function tearDownAfterClass()
    {
        self::removeDatabaseTable();

        parent::tearDownAfterClass();
    }

    /**
     * Create a table in the database especially for this test.
     */
    protected static function createDatabaseTable()
    {
        $db = self::createDatabaseStatic();

        $db->execute('CREATE TABLE IF NOT EXISTS ' . self::TABLE_NAME . ' (oxid CHAR(32), oxuserid CHAR(32)) ENGINE innoDb;');
    }

    /**
     * Drop the test database table.
     */
    protected static function removeDatabaseTable()
    {
        $db = self::createDatabaseStatic();

        $db->execute('DROP TABLE ' . self::TABLE_NAME . ';');
    }

    /**
     * Create the database object under test.
     *
     * @return Doctrine The database object under test.
     */
    protected function createDatabase()
    {
        return new Doctrine();
    }

    /**
     * Close the database connection.
     */
    protected function closeConnection()
    {
        $this->database->closeConnection();
    }

    /**
     * Test, that the method 'MoveNext' works for an empty result set.
     */
    public function testMoveNextWithEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $methodResult = $resultSet->MoveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'MoveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_3), $resultSet->fields);
        $this->assertTrue($methodResult);
    }

    /**
     * Test, that the method 'MoveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSetReachingEnd()
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $resultSet->MoveNext();
        $resultSet->MoveNext();
        $methodResult = $resultSet->MoveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);

        $methodResult = $resultSet->MoveNext();

        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
        $this->assertFalse($methodResult);
    }

    /**
     * Test, that the method 'MoveNext' works for a non empty result set and the fetch mode associative array.
     */
    public function testMoveNextWithNonEmptyResultSetFetchModeAssociative()
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode(\PDO::FETCH_ASSOC);
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);
        $this->initializeDatabase();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_1), $resultSet->fields);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array('OXID' => self::FIXTURE_OXID_2), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->MoveNext();

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
     * Test, that the method 'GetArray' works as expected.
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

        $result = $resultSet->GetArray($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'GetArray' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetArraySequentialCalls()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->GetArray(1);
        $resultTwo = $resultSet->GetArray(1);
        $resultThree = $resultSet->GetArray(1);

        $this->assertSame($resultOne, array(array(self::FIXTURE_OXID_1)));
        $this->assertSame($resultTwo, array(array(self::FIXTURE_OXID_2)));
        $this->assertSame($resultThree, array(array(self::FIXTURE_OXID_3)));
    }

    /**
     * Test, that the method 'GetArray' works as expected, if we set first a fetch mode different from the default.
     */
    public function testGetArrayWithDifferentFetchMode()
    {
        $this->loadFixtureToTestTable();
        $oldFetchMode = $this->database->setFetchMode(3);

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->GetArray(1);
        $resultTwo = $resultSet->GetArray(1);
        $resultThree = $resultSet->GetArray(1);

        $this->database->setFetchMode($oldFetchMode);

        $expectedOne = array(array('OXID' => self::FIXTURE_OXID_1, self::FIXTURE_OXID_1));
        $expectedTwo = array(array('OXID' => self::FIXTURE_OXID_2, self::FIXTURE_OXID_2));
        $expectedThree = array(array('OXID' => self::FIXTURE_OXID_3, self::FIXTURE_OXID_3));

        $this->assertArrayContentSame($resultOne, $expectedOne);
        $this->assertArrayContentSame($resultTwo, $expectedTwo);
        $this->assertArrayContentSame($resultThree, $expectedThree);
    }

    /**
     * Test, that the method 'GetRows' works as expected.
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

        $result = $resultSet->GetRows($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'GetRows' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetRowsSequentialCalls()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $resultOne = $resultSet->GetRows(1);
        $resultTwo = $resultSet->GetRows(1);
        $resultThree = $resultSet->GetRows(1);

        $this->assertSame($resultOne, array(array(self::FIXTURE_OXID_1)));
        $this->assertSame($resultTwo, array(array(self::FIXTURE_OXID_2)));
        $this->assertSame($resultThree, array(array(self::FIXTURE_OXID_3)));
    }

    /**
     * Test, that the method 'FetchField' works as expected.
     */
    public function testFetchField()
    {
        $this->loadFixtureToTestTable();
        $resultSet = $this->database->select('SELECT * FROM ' . self::TABLE_NAME);

        $columnInformationOne = $resultSet->FetchField(0);

        $this->assertSame('stdClass', get_class($columnInformationOne));

        /**
         * We are skipping the doctrine unsupported features here.
         */
        $fields = array(
            'name'        => 'oxid',
            'table'       => self::TABLE_NAME,
            'max_length'  => 96,
            'not_null'    => 0,
            'primary_key' => 0,
            'type'        => 'string',
            // 'unsigned'     => 0,
            // 'zerofill'     => 0
            // 'def'          => '',
            // 'multiple_key' => 0,
            // 'unique_key'   => 0,
            // 'numeric'      => 0,
            // 'blob'         => 0,
        );

        foreach ($fields as $attributeName => $attributeValue) {
            $this->assertObjectHasAttributeWithValue($columnInformationOne, $attributeName, $attributeValue);
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
     * Test, that the method 'FieldCount' works as expected.
     *
     * @dataProvider dataProviderTestFieldCount
     *
     * @param string $query         The sql statement we want to test.
     * @param int    $expectedCount The expected number of fields.
     */
    public function testFieldCount($query, $expectedCount)
    {
        $resultSet = $this->database->select($query);

        $this->assertSame($expectedCount, $resultSet->FieldCount());
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
     * Test, that the method Fields works as expected.
     *
     * @dataProvider dataProviderTestFields
     *
     * @param string $query                The sql statement to execute.
     * @param mixed  $parameter            The parameter for the Fields method.
     * @param bool   $loadFixture          Should the fixture be loaded to the test database table?
     * @param mixed  $expected             The expected result of the Fields method under the given specification.
     * @param bool   $fetchModeAssociative Should the fetch mode be set to associative array before running the statement?
     */
    public function testFields($query, $parameter, $loadFixture, $expected, $fetchModeAssociative = false)
    {
        $oldFetchMode = null;
            
        if ($loadFixture) {
            $this->loadFixtureToTestTable();
        }
        if ($fetchModeAssociative) {
            $oldFetchMode = $this->database->setFetchMode(2);
        }

        $resultSet = $this->database->select($query);
        $result = $resultSet->Fields($parameter);

        if ($fetchModeAssociative) {
            $this->database->setFetchMode($oldFetchMode);
        }

        $this->cleanTestTable();
        $this->assertSame($expected, $result);
    }

    /**
     * Test, that the method 'Move' works with an empty result set.
     */
    public function testMoveWithEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $methodResult = $resultSet->Move(7);

        $this->assertFalse($methodResult);
        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
    }

    /**
     * @return array The parameters we want to use for the testMove method.
     */
    public function dataProviderTestMove()
    {
        return array(
            array(2, array(self::FIXTURE_OXID_3)),
            array(0, array(self::FIXTURE_OXID_1)),
            array(1, array(self::FIXTURE_OXID_2)),
            array(300, array(self::FIXTURE_OXID_3)) // the last row (no. 239) stays
        );
    }

    /**
     * Test the method 'Move' with the parameters given by the corresponding data provider.
     *
     * @dataProvider dataProviderTestMove
     *
     * @param int   $moveTo         The index of the line we want to check.
     * @param array $expectedFields The expected values in the given line.
     *
     * @return mixed|\object_ResultSet|DoctrineResultSet The result set after the given MoveTo method call.
     */
    public function testMove($moveTo, $expectedFields)
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->Move($moveTo);

        $this->assertTrue($methodResult);
        $this->assertSame($expectedFields, $resultSet->fields);
        $this->assertFalse($resultSet->EOF);

        return $resultSet;
    }

    /**
     * Test, that the method 'MoveFirst' works as expected for an empty result set.
     */
    public function testMoveFirstEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->MoveFirst();

        $this->assertTrue($methodResult);
        $this->assertSame(false, $resultSet->fields);
        $this->assertTrue($resultSet->EOF);
    }

    /**
     * Test, that the method 'MoveFirst' works as expected for a non empty result set.
     */
    public function testMoveFirstNonEmptyResultSet()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->testMove(2, array(self::FIXTURE_OXID_3));

        $methodResult = $resultSet->MoveFirst();

        $this->assertTrue($methodResult);
        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);
        $this->assertFalse($resultSet->EOF);
    }

    /**
     * Test, that the method 'MoveFirst' works as expected for a non empty result set,
     * if we move to nearly the end of the rows.
     */
    public function testMoveFirstNonEmptyResultSetNearlyEndOfRows()
    {
        $resultSet = $this->testMove(2, array(self::FIXTURE_OXID_3));

        $methodResult = $resultSet->MoveFirst();

        $this->assertTrue($methodResult);
        $this->assertSame(array(self::FIXTURE_OXID_1), $resultSet->fields);
        $this->assertFalse($resultSet->EOF);
    }

    /**
     * Test, that the method 'MoveLast' works as expected for an empty result set.
     */
    public function testMoveLastEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->MoveLast();

        $this->assertFalse($methodResult);
        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
    }

    /**
     * Test, that the method 'MoveLast' works as expected for a non empty result set, when we call it several times.
     */
    public function testMoveLastNonEmptyResultSet()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $methodResult = $resultSet->MoveLast();

        $this->assertTrue($methodResult);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame($resultSet->fields, array(self::FIXTURE_OXID_3));
    }

    /**
     * Test, that the method 'MoveLast' works as expected for a non empty result set, when we call it several times.
     */
    public function testMoveLastNonEmptyResultSetSequentialCalls()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID;');

        $resultSet->MoveLast();
        $methodResult = $resultSet->MoveLast();

        $this->assertTrue($methodResult);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame($resultSet->fields, array(self::FIXTURE_OXID_3));
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
        $this->assertSame(self::FIXTURE_OXID_1, $row[0]);
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
     * Test, that the method 'Close' works as expected for an empty result set.
     */
    public function testCloseEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $resultSet->Close();

        $this->assertTrue($resultSet->EOF);
        $this->assertSame(array(), $resultSet->fields);
    }

    /**
     * Test, that the method 'Close' works as expected for an empty result set with fetching a row after closing the cursor.
     */
    public function testCloseEmptyResultSetWithFetchingAfterClosing()
    {
        $resultSet = $this->testCreationWithRealEmptyResult();

        $resultSet->Close();

        $firstRow = $resultSet->fetchRow();

        $this->assertFalse($firstRow);
        $this->assertTrue($resultSet->EOF);
        $this->assertSame(array(), $resultSet->fields);
    }

    /**
     * Test, that the method 'Close' works as expected for a non empty result set.
     */
    public function testCloseNonEmptyResultSet()
    {
        $resultSet = $this->testCreationWithRealNonEmptyResult();

        $firstRow = $resultSet->fetchRow();

        $resultSet->Close();

        $this->assertSame(array(self::FIXTURE_OXID_1), $firstRow);
        $this->assertFalse($resultSet->EOF);
        $this->assertSame(array(), $resultSet->fields);
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
