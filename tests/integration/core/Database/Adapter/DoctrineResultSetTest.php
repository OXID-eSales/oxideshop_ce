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

use OxidEsales\TestingLibrary\UnitTestCase;
use Doctrine\DBAL\Driver\PDOStatement;
use OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet;
use OxidEsales\Eshop\Core\Database\Doctrine;

/**
 * Tests for our database object.
 *
 * @group doctrine
 */
class Integration_Core_Database_Adapter_DoctrineResultSetTest extends UnitTestCase
{

    /**
     * @var Doctrine|oxLegacyDb The database to test.
     */
    protected $database = null;

    /**
     * @var bool Should this test use the legacy database for the tests?
     */
    protected $useLegacyDatabase = false;

    /**
     * @var string The name of the class, including the complete namespace.
     */
    const CLASS_NAME_WITH_PATH = 'OxidEsales\Eshop\Core\Database\Adapter\DoctrineResultSet';

    /**
     * @var string The first OXID of the OXARTICLES
     */
    const FIRST_OXARTICLE_OXID = '09602cddb5af0aba745293d08ae6bcf6';

    public function setUp()
    {
        parent::setUp();

        $this->createDatabase();
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
        $this->assertEquals(array('09602cddb5af0aba745293d08ae6bcf6'), $resultSet->fields);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertEquals(array('09620040146118fbc4b7eef6a0faf072'), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertEquals(array('0962081a5693597654fd2887af7a6095'), $resultSet->fields);
        $this->assertTrue($methodResult);
    }

    /**
     * Test, that the method 'MoveNext' works for a non empty result set.
     */
    public function testMoveNextWithNonEmptyResultSetReachingEnd()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxvendor;');

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
        $this->database->setFetchMode(PDO::FETCH_ASSOC);
        $resultSet = $this->database->select('SELECT OXID FROM oxarticles;');
        $this->createDatabase();

        $this->assertFalse($resultSet->EOF);
        $this->assertEquals(array('OXID' => '09602cddb5af0aba745293d08ae6bcf6'), $resultSet->fields);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertEquals(array('OXID' => '09620040146118fbc4b7eef6a0faf072'), $resultSet->fields);
        $this->assertTrue($methodResult);

        $methodResult = $resultSet->MoveNext();

        $this->assertFalse($resultSet->EOF);
        $this->assertEquals(array('OXID' => '0962081a5693597654fd2887af7a6095'), $resultSet->fields);
        $this->assertTrue($methodResult);
    }

    /**
     * @return array The parameters we want to use for the testGetRows and testGetArray methods.
     */
    public function dataProvider_testGetRows_testGetArray()
    {
        return array(
            array('SELECT OXID FROM oxorderfiles', 0, array()),
            array('SELECT OXID FROM oxorderfiles', 1, array()),
            array('SELECT OXID FROM oxorderfiles', 10, array()),
            array('SELECT OXID FROM oxvendor', 0, array()),
            array('SELECT OXID FROM oxvendor', 1, array(array('9437def212dc37c66f90cc249143510a'))),
            array('SELECT OXID FROM oxvendor', 5, array(array('9437def212dc37c66f90cc249143510a'), array('d2e44d9b31fcce448.08890330'), array('d2e44d9b32fd2c224.65443178'))),
        );
    }

    /**
     * Test, that the method 'GetArray' works as expected.
     *
     * @dataProvider dataProvider_testGetRows_testGetArray
     */
    public function testGetArray($query, $numberOfRows, $expectedArray)
    {
        $resultSet = $this->database->select($query);

        $result = $resultSet->GetArray($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'GetArray' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetArraySequentialCalls()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxvendor ORDER BY OXID');

        $resultOne = $resultSet->GetArray(1);
        $resultTwo = $resultSet->GetArray(1);
        $resultThree = $resultSet->GetArray(1);

        $this->assertSame($resultOne, array(array('9437def212dc37c66f90cc249143510a')));
        $this->assertSame($resultTwo, array(array('d2e44d9b31fcce448.08890330')));
        $this->assertSame($resultThree, array(array('d2e44d9b32fd2c224.65443178')));
    }

    /**
     * Test, that the method 'GetArray' works as expected, if we set first a fetch mode different from the default.
     */
    public function testGetArrayWithDifferentFetchMode()
    {
        $oldFetchMode = $this->database->setFetchMode(3);
        $resultSet = $this->database->select('SELECT OXID FROM oxvendor ORDER BY OXID');

        $resultOne = $resultSet->GetArray(1);
        $resultTwo = $resultSet->GetArray(1);
        $resultThree = $resultSet->GetArray(1);

        $this->database->setFetchMode($oldFetchMode);

        $expectedOne = array(array('OXID' => '9437def212dc37c66f90cc249143510a', '9437def212dc37c66f90cc249143510a'));
        $expectedTwo = array(array('OXID' => 'd2e44d9b31fcce448.08890330', 'd2e44d9b31fcce448.08890330'));
        $expectedThree = array(array('OXID' => 'd2e44d9b32fd2c224.65443178', 'd2e44d9b32fd2c224.65443178'));

        $this->assertArrayContentSame($resultOne, $expectedOne);
        $this->assertArrayContentSame($resultTwo, $expectedTwo);
        $this->assertArrayContentSame($resultThree, $expectedThree);
    }

    /**
     * Test, that the method 'GetRows' works as expected.
     *
     * @dataProvider dataProvider_testGetRows_testGetArray
     *
     * @param string $query         The sql statement to execute.
     * @param int    $numberOfRows  The number of rows to fetch.
     * @param array  $expectedArray The resulting array, which we expect.
     */
    public function testGetRows($query, $numberOfRows, $expectedArray)
    {
        $resultSet = $this->database->select($query);

        $result = $resultSet->GetRows($numberOfRows);

        $this->assertSame($expectedArray, $result);
    }

    /**
     * Test, that the method 'GetRows' works as expected, if we call it consecutive. Thereby we assure, that the internal row pointer is used correct.
     */
    public function testGetRowsSequentialCalls()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxvendor ORDER BY OXID');

        $resultOne = $resultSet->GetRows(1);
        $resultTwo = $resultSet->GetRows(1);
        $resultThree = $resultSet->GetRows(1);

        $this->assertSame($resultOne, array(array('9437def212dc37c66f90cc249143510a')));
        $this->assertSame($resultTwo, array(array('d2e44d9b31fcce448.08890330')));
        $this->assertSame($resultThree, array(array('d2e44d9b32fd2c224.65443178')));
    }

    /**
     * Test, that the method 'FetchField' works as expected.
     */
    public function testFetchField()
    {
        $resultSet = $this->database->select('SELECT * FROM oxvendor');

        $columnInformationOne = $resultSet->FetchField(0);

        $this->assertEquals('stdClass', get_class($columnInformationOne));

        /**
         * We are skipping the doctrine unsupported features here.
         */
        $fields = array(
            'name'        => 'OXID',
            'table'       => 'oxvendor',
            'max_length'  => 96,
            'not_null'    => 1,
            'primary_key' => 1,
            'type'        => 'string',
            // 'unsigned'     => 0,
            // 'zerofill'     => 0
            // 'def'          => '',
            // 'multiple_key' => 0,
            // 'unique_key'   => 0,
            // 'numeric'      => 0,
            // 'blob'         => 0,
        );

        foreach ($fields as $key => $value) {
            $this->assertTrue(isset($columnInformationOne->$key), 'Missing field "' . $key . '".');
            $this->assertSame($value, $columnInformationOne->$key);
        }
    }

    /**
     * @return array The parameters we want to use for the testMove method.
     */
    public function dataProvider_testFieldCount()
    {
        return array(
            array('SELECT OXID FROM oxorderfiles;', 1),
            array('SELECT OXID FROM oxvouchers;', 1),
            array('SELECT * FROM oxvouchers;', 9)
        );
    }

    /**
     * Test, that the method 'FieldCount' works as expected.
     *
     * @dataProvider dataProvider_testFieldCount
     */
    public function testFieldCount($query, $count)
    {
        $resultSet = $this->database->select($query);

        $this->assertEquals($count, $resultSet->FieldCount());
    }

    /**
     * @return array The parameters we want to use for the testFields method.
     */
    public function dataProvider_testFields()
    {
        return array(
            array('SELECT OXID FROM oxvouchers', 0, false),
            array('SELECT OXID FROM oxorderfiles', 'OXID', null),
            array('SELECT OXID FROM oxarticles ORDER BY OXID', 0, array('09602cddb5af0aba745293d08ae6bcf6')),
            array('SELECT OXID FROM oxarticles ORDER BY OXID', 'OXID', null),
            array('SELECT OXID,OXARTNUM FROM oxarticles ORDER BY OXID', 0, array('09602cddb5af0aba745293d08ae6bcf6', '0802-85-823-7-1')),
            array('SELECT OXID,OXARTNUM FROM oxarticles ORDER BY OXID', 1, '0802-85-823-7-1'),
            array('SELECT OXID,OXARTNUM FROM oxarticles ORDER BY OXID', 'OXID', '09602cddb5af0aba745293d08ae6bcf6', true),
            array('SELECT OXID,OXARTNUM FROM oxarticles ORDER BY OXID', 0, array('OXID' => '09602cddb5af0aba745293d08ae6bcf6', 'OXARTNUM' => '0802-85-823-7-1'), true),
            array('SELECT OXID,OXARTNUM FROM oxarticles ORDER BY OXID', 'NOTNULL', null, true),
        );
    }

    /**
     * Test, that the method Fields works as expected.
     *
     * @dataProvider dataProvider_testFields
     *
     * @param string $query                The sql statement to execute.
     * @param mixed  $parameter            The parameter for the Fields method.
     * @param mixed  $expected             The expected result of the Fields method under the given specification.
     * @param bool   $fetchModeAssociative Should the fetch mode be set to associative array before running the statement?
     */
    public function testFields($query, $parameter, $expected, $fetchModeAssociative = false)
    {
        if ($fetchModeAssociative) {
            $oldFetchMode = $this->database->setFetchMode(2);
        }

        $resultSet = $this->database->select($query);
        $result = $resultSet->Fields($parameter);

        if ($fetchModeAssociative) {
            $this->database->setFetchMode($oldFetchMode);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Test, that the method 'Move' works with an empty result set.
     */
    public function testMoveWithEmptyResultSet()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxvouchers;');

        $methodResult = $resultSet->Move(7);

        $this->assertFalse($methodResult);
        $this->assertTrue($resultSet->EOF);
        $this->assertFalse($resultSet->fields);
    }

    /**
     * @return array The parameters we want to use for the testMove method.
     */
    public function dataProvider_testMove()
    {
        return array(
            array(2, array('0962081a5693597654fd2887af7a6095')),
            array(0, array('09602cddb5af0aba745293d08ae6bcf6')),
            array(1, array('09620040146118fbc4b7eef6a0faf072')),
            array(300, array('a7c44be4a5ddee114.67356237')) // the last row (no. 239) stays
        );
    }

    /**
     * Test the method 'Move' with the parameters given by the corresponding data provider.
     *
     * @dataProvider dataProvider_testMove
     *
     * @param int   $moveTo         The index of the line we want to check.
     * @param array $expectedFields The expected values in the given line.
     */
    public function testMove($moveTo, $expectedFields)
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxarticles ORDER BY OXID;');

        $methodResult = $resultSet->Move($moveTo);

        $this->assertTrue($methodResult);
        $this->assertEquals($expectedFields, $resultSet->fields);
        $this->assertFalse($resultSet->EOF);
    }

    /**
     * Test, that the result set of an empty select works as expected.
     *
     * @return DoctrineResultSet The empty result set.
     */
    public function testCreationWithRealEmptyResult()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxvouchers;');

        $this->assertDoctrineResultSet($resultSet);
        $this->assertEquals(0, $resultSet->recordCount());

        return $resultSet;
    }

    /**
     * Test, that the result set of a non empty select works as expected.
     *
     * @return DoctrineResultSet The non empty result set.
     */
    public function testCreationWithRealNonEmptyResult()
    {
        $resultSet = $this->database->select('SELECT OXID FROM oxarticles;');

        $this->assertDoctrineResultSet($resultSet);
        $this->assertGreaterThan(200, $resultSet->recordCount());

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
        $this->assertEquals(self::FIRST_OXARTICLE_OXID, $row[0]);
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
        $this->assertGreaterThan(200, count($rows));
        $this->assertEquals(self::FIRST_OXARTICLE_OXID, $rows[0][0]);
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
     * Create the database, we want to test.
     */
    private function createDatabase()
    {
        if ($this->useLegacyDatabase) {
            $this->database = oxDb::getDb();
        } else {
            $this->database = new Doctrine();
        }
    }

    /**
     * Assert, that the given object is a doctrine result set.
     *
     * @param DoctrineResultSet $resultSet The object to check.
     */
    private function assertDoctrineResultSet($resultSet)
    {
        if ($this->useLegacyDatabase) {
            $this->assertEquals('object_ResultSet', get_class($resultSet));
        } else {
            $this->assertEquals(self::CLASS_NAME_WITH_PATH, get_class($resultSet));
        }
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
