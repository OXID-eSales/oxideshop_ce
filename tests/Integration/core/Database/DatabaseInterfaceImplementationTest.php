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

namespace OxidEsales\Eshop\Tests\Integration\Core\Database;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Registry;
use ReflectionClass;

/**
 * Abstract base class for all implementations of the DatabaseInterface.
 * All implementation MUST extend this class in order to assure the correct implementation of the interface.
 * If the interface is changed update or add the tests here.
 * Tests, which do not test mere interface implementation should go to the concrete tests.
 * If the implementation is changed update or add the tests in the concrete class e.g. DoctrineTest.
 *
 * @package OxidEsales\Eshop\Tests\Integration\Core\Database
 *
 * @group   database-adapter
 */
abstract class DatabaseInterfaceImplementationTest extends DatabaseInterfaceImplementationBaseTest
{
    /**
     * The data provider for the method testGetAllForAllFetchModes.
     *
     * @return array The parameters for testGetAllForAllFetchModes.
     */
    public function dataProviderTestGetAllForAllFetchModes()
    {
        return array(
            /**
             *
             * DatabaseInterface::FETCH_MODE_DEFAULT
             * This returns the same as DatabaseInterface::FETCH_MODE_BOTH and a funny aspect of our beloved ADOdb lite
             *
             */
            array( // fetch mode "default", this is not the "default fetch mode" and an empty result
                   DatabaseInterface::FETCH_MODE_DEFAULT,
                   'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE 0',
                   array()
            ),
            array( // fetch mode "default", this is not the "default fetch mode" and one column
                   DatabaseInterface::FETCH_MODE_DEFAULT,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1, 0 => self::FIXTURE_OXID_1),
                       array('OXID' => self::FIXTURE_OXID_2, 0 => self::FIXTURE_OXID_2),
                       array('OXID' => self::FIXTURE_OXID_3, 0 => self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode "default", this is not the "default fetch mode" and multiple columns
                   DatabaseInterface::FETCH_MODE_DEFAULT,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME,
                   array(
                       array(
                           'OXID'     => self::FIXTURE_OXID_1,
                           'OXUSERID' => self::FIXTURE_OXUSERID_1,
                           0          => self::FIXTURE_OXID_1,
                           1          => self::FIXTURE_OXUSERID_1,
                       ),
                       array(
                           'OXID'     => self::FIXTURE_OXID_2,
                           'OXUSERID' => self::FIXTURE_OXUSERID_2,
                           0          => self::FIXTURE_OXID_2,
                           1          => self::FIXTURE_OXUSERID_2,
                       ),
                       array(
                           'OXID'     => self::FIXTURE_OXID_3,
                           'OXUSERID' => self::FIXTURE_OXUSERID_3,
                           0          => self::FIXTURE_OXID_3,
                           1          => self::FIXTURE_OXUSERID_3,
                       )
                   )
            ),
            /**
             *
             * DatabaseInterface::FETCH_MODE_NUM
             *
             */
            array( // fetch mode numeric and an INSERT statement
                   DatabaseInterface::FETCH_MODE_NUM,
                   'INSERT INTO ' . self::TABLE_NAME . ' VALUES (\'a\', \'b\')',
                   array()
            ),
            array( // fetch mode numeric and an empty result
                   DatabaseInterface::FETCH_MODE_NUM,
                   'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE 0',
                   array()
            ),
            array( // fetch mode numeric and one column
                   DatabaseInterface::FETCH_MODE_NUM,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1),
                       array(self::FIXTURE_OXID_2),
                       array(self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode numeric and multiple columns
                   DatabaseInterface::FETCH_MODE_NUM,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1),
                       array(self::FIXTURE_OXID_2, self::FIXTURE_OXUSERID_2),
                       array(self::FIXTURE_OXID_3, self::FIXTURE_OXUSERID_3)
                   )
            ),
            /**
             *
             * DatabaseInterface::FETCH_MODE_ASSOC
             *
             */
            array( // fetch mode associative and an empty result
                   DatabaseInterface::FETCH_MODE_ASSOC,
                   'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE 0',
                   array()
            ),
            array( // fetch mode associative and one column
                   DatabaseInterface::FETCH_MODE_ASSOC,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1),
                       array('OXID' => self::FIXTURE_OXID_2),
                       array('OXID' => self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode associative and multiple columns
                   DatabaseInterface::FETCH_MODE_ASSOC,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1, 'OXUSERID' => self::FIXTURE_OXUSERID_1),
                       array('OXID' => self::FIXTURE_OXID_2, 'OXUSERID' => self::FIXTURE_OXUSERID_2),
                       array('OXID' => self::FIXTURE_OXID_3, 'OXUSERID' => self::FIXTURE_OXUSERID_3)
                   )
            ),
            /**
             *
             * DatabaseInterface::FETCH_MODE_BOTH
             *
             */
            array( // fetch mode both and an empty result
                   DatabaseInterface::FETCH_MODE_BOTH,
                   'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE 0',
                   array()
            ),
            array( // fetch mode both and one column
                   DatabaseInterface::FETCH_MODE_BOTH,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1, 0 => self::FIXTURE_OXID_1),
                       array('OXID' => self::FIXTURE_OXID_2, 0 => self::FIXTURE_OXID_2),
                       array('OXID' => self::FIXTURE_OXID_3, 0 => self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode both and multiple columns
                   DatabaseInterface::FETCH_MODE_BOTH,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME,
                   array(
                       array(
                           'OXID'     => self::FIXTURE_OXID_1,
                           'OXUSERID' => self::FIXTURE_OXUSERID_1,
                           0          => self::FIXTURE_OXID_1,
                           1          => self::FIXTURE_OXUSERID_1,
                       ),
                       array(
                           'OXID'     => self::FIXTURE_OXID_2,
                           'OXUSERID' => self::FIXTURE_OXUSERID_2,
                           0          => self::FIXTURE_OXID_2,
                           1          => self::FIXTURE_OXUSERID_2,
                       ),
                       array(
                           'OXID'     => self::FIXTURE_OXID_3,
                           'OXUSERID' => self::FIXTURE_OXUSERID_3,
                           0          => self::FIXTURE_OXID_3,
                           1          => self::FIXTURE_OXUSERID_3,
                       )
                   )
            ),

        );
    }

    /**
     * Test, that the method 'getAll' returns the expected results for the all possible fetch modes.
     *
     * @dataProvider dataProviderTestGetAllForAllFetchModes
     *
     * @param int    $fetchMode    The fetch mode we want to test.
     * @param string $sql          The query we want to test.
     * @param array  $expectedRows The rows we expect.
     */
    public function testGetAllForAllFetchModes($fetchMode, $sql, $expectedRows)
    {
        $this->loadFixtureToTestTable();
        $this->database->setFetchMode($fetchMode);

        $rows = $this->database->getAll($sql);

        $this->assertInternalType('array', $rows, 'Expected an array as result!');
        // sometimes the array gets filled in different order, we sort them to be sure, the content is same
        $this->assertEquals($expectedRows, $rows);
    }


    /**
     * Data provider for the default fetch mode
     *
     * @return array
     */
    public function dataProviderTestFetchAllDefaultFetchMode()
    {
        return array(
            array(
                'On default fetch mode, DatabaseInterface::select() will return an empty array for an empty result',
                'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE 0',
                array()
            ),
            array( // fetch mode default and one column
                   'On default fetch mode, DatabaseInterface::select() will return an array with numeric key for a non empty result',
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1),
                       array(self::FIXTURE_OXID_2),
                       array(self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode default and multiple columns
                   'On default fetch mode, DatabaseInterface::select() will return an array with numeric key for a non empty result',
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1, '1'),
                       array(self::FIXTURE_OXID_2, '2'),
                       array(self::FIXTURE_OXID_3, '3')
                   )
            ),

        );
    }

    /**
     * Test that the return values of a select statement are as expected, if no fetch mode has been set on the connection.
     * In this case the fetch mode should be DatabaseInterface::FETCH_MODE_NUM, as it is the default fetch mode.
     *
     * @dataProvider dataProviderTestFetchAllDefaultFetchMode
     *
     * @param string $assertionMessage A message explaining the assertion
     * @param string $sql              The SQL query to be executed
     * @param array  $expectedRows     The expected result as an array
     */
    public function testGetAllWithDefaultFetchMode($assertionMessage, $sql, $expectedRows)
    {
        /** @var DatabaseInterface $database Get a fresh instance of the database handler */
        $database = $this->createDatabase();

        $this->loadFixtureToTestTable($database);

        $actualRows = $database->getAll($sql);

        $this->assertInternalType('array', $actualRows, 'Expected an array as result!');
        // sometimes the array gets filled in different order, we sort them to be sure, the content is same
        $this->assertSame(sort($expectedRows), sort($actualRows), $assertionMessage);
    }

    /**
     * Test, that the method 'select' reacts as expected, when called with parameters.
     */
    public function testSelectWithParameters()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID = ?', array(self::FIXTURE_OXID_2), false);

        $result = $resultSet->getAll();

        $this->assertEquals(array(array(self::FIXTURE_OXID_2)), $result);
    }

    public function testSelectWithNonReadStatementThrowsException()
    {
        $expectedExceptionClass = $this->getDatabaseExceptionClassName();

        $this->setExpectedException($expectedExceptionClass);

        $this->database->select('INSERT INTO ' . self::TABLE_NAME . ' VALUES (\'a\',\'b\')');
    }


    public function testSelectPreparedWithInvalidParameterDoesNotThrowException()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID = ?', array(array('key' => 'value')), false);

        $result = $resultSet->getAll();

        $this->assertEquals(array(), $result);
    }

    /**
     * Test, that the method 'selectLimit' reacts as expected, when called with parameters.
     */
    public function testSelectLimitWithParameters()
    {
        $this->loadFixtureToTestTable();

        $resultSet = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID <> ?', array(self::FIXTURE_OXID_2), false);

        $result = $resultSet->getAll();

        $this->assertEquals(array(array(self::FIXTURE_OXID_1), array(self::FIXTURE_OXID_3)), $result);
    }

    /**
     * Data provider for testSelectLimit.
     *
     * @return array The parameters we give into testSelectLimit.
     */
    public function dataProviderTestSelectLimitForDifferentLimitAndOffsetValues()
    {
        return array(
            array(
                'If parameter rowCount is integer 0, no rows are returned at all',
                0, // row count
                0, // offset
                [] // expected result
            ),
            array(
                'If parameter rowCount is string "2" and offset is string "0", the first 2 rows will be returned',
                "2", // row count as a string
                "0", // offset as string
                [
                    [self::FIXTURE_OXID_1], [self::FIXTURE_OXID_2]  // expected result
                ]
            ),
            array(
                'If parameter rowCount has the value 2 and offset has the value 0, the first 2 rows will be returned',
                2, // row count
                0, // offset
                [
                    [self::FIXTURE_OXID_1], [self::FIXTURE_OXID_2]  // expected result
                ]
            ),
            array(
                'If parameter rowCount has the value 2 and offset has the value 1, the last 2 rows will be returned',
                2, // row count
                1, // offset
                [
                    [self::FIXTURE_OXID_2], [self::FIXTURE_OXID_3] // expected result
                ]
            ),
        );
    }

    /**
     * Test, that the method 'selectLimit' returns the expected rows from the database for different
     * values of limit and offset.
     *
     * This test assumes that there are at least 3 entries in the table.
     *
     * @dataProvider dataProviderTestSelectLimitForDifferentLimitAndOffsetValues
     *
     * @param string $assertionMessage A message explaining the assertion
     * @param int    $rowCount         Maximum number of rows to return
     * @param int    $offset           Offset of the first row to return
     * @param array  $expectedResult   The expected result of the method call.
     */
    public function testSelectLimitReturnsExpectedResultForDifferentOffsetAndLimit($assertionMessage, $rowCount, $offset, $expectedResult)
    {
        $this->loadFixtureToTestTable();
        $sql = 'SELECT OXID FROM ' . self::TABLE_NAME . ' WHERE OXID IN (' .
               '"' . self::FIXTURE_OXID_1 . '",' .
               '"' . self::FIXTURE_OXID_2 . '",' .
               '"' . self::FIXTURE_OXID_3 . '"' .
               ')';

        $resultSet = $this->database->selectLimit($sql, $rowCount, $offset);
        $actualResult = $resultSet->getAll();

        $this->assertSame($expectedResult, $actualResult, $assertionMessage);
    }

    /**
     * Test, that the method 'select' works with an empty result set for the select query.
     */
    public function testSelectWithEmptyResultSelect()
    {
        $result = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME);

        $expectedRows = array();
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }

    /**
     * Test, that the method 'select' works with an empty result set for the select query,
     * whereby the select clause is not on the first char.
     */
    public function testExecuteWithEmptyResultAndSelectNotOnFirstChar()
    {
        $result = $this->database->select('   SELECT OXID FROM ' . self::TABLE_NAME);

        $expectedRows = array();
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }

    /**
     * Test, that the method 'select' works with a non empty result set for the select query.
     */
    public function testExecuteWithNonEmptySelect()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

        $this->assertFalse($result->EOF);
        $this->assertSame(array(self::FIXTURE_OXID_1), $result->fields);

        $expectedRows = array(
            array(self::FIXTURE_OXID_1),
            array(self::FIXTURE_OXID_2),
            array(self::FIXTURE_OXID_3)
        );
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }


    /**
     * Test that the expected exception is thrown when passing an invalid non "SELECT" query.
     */
    public function testExecuteThrowsExceptionForInvalidNonSelectQueryString()
    {
        $expectedExceptionClass = $this->getDatabaseExceptionClassName();

        $this->setExpectedException($expectedExceptionClass);

        $this->database->execute('SOME INVALID QUERY');
    }

    /**
     * Test that the expected exception is thrown when passing an invalid non "SELECT" query.
     */
    public function testSelectThrowsExceptionForInvalidSelectQueryString()
    {
        $expectedExceptionClass = $this->getDatabaseExceptionClassName();

        $this->setExpectedException($expectedExceptionClass);

        $this->database->select(
            'SELECT SOME INVALID QUERY',
            array(),
            false
        );
    }

    /**
     * Test, that the fetch mode set works as expected and retrieves the last set fetch mode.
     */
    public function testSetFetchMode()
    {
        $this->loadFixtureToTestTable();

        // check normal (associative array) case
        $row = $this->fetchFirstTestTableOxId();
        $this->assertInternalType('array', $row);
        $this->assertSame(array(0), array_keys($row));

        // check numeric array case
        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_NUM);
        $row = $this->fetchFirstTestTableOxId();

        // check result
        $this->assertInternalType('array', $row);
        $this->assertSame(array(0), array_keys($row));
    }

    /**
     * Test, that the set of the transaction isolation level works.
     */
    public function testSetTransactionIsolationLevel()
    {
        $transactionIsolationLevelPre = $this->fetchTransactionIsolationLevel();

        $expectedLevel = 'READ COMMITTED';
        $this->database->setTransactionIsolationLevel($expectedLevel);
        $transactionIsolationLevel = $this->fetchTransactionIsolationLevel();

        $this->assertSame($expectedLevel, $transactionIsolationLevel);

        $this->database->setTransactionIsolationLevel($transactionIsolationLevelPre);
        $transactionIsolationLevel = $this->fetchTransactionIsolationLevel();

        $this->assertSame($transactionIsolationLevelPre, $transactionIsolationLevel);
    }

    /**
     * Test, that the method 'getCol' works without parameters and an empty result.
     */
    public function testGetColWithoutParametersEmptyResult()
    {
        $result = $this->database->getCol("SELECT OXID FROM " . self::TABLE_NAME);

        $this->assertInternalType('array', $result);
        $this->assertSame(0, count($result));
    }

    /**
     * Test, that the method 'getCol' works without parameters and a non empty result.
     */
    public function testGetColWithoutParameters()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getCol("SELECT OXUSERID FROM " . self::TABLE_NAME);

        $this->assertInternalType('array', $result);
        $this->assertSame(3, count($result));
        $this->assertSame(array(self::FIXTURE_OXUSERID_1, self::FIXTURE_OXUSERID_2, self::FIXTURE_OXUSERID_3), $result);
    }

    /**
     * Test, that the method 'getCol' works without parameters and a non empty result.
     */
    public function testGetColDoesNotDependOnFetchMode()
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode(DatabaseInterface::FETCH_MODE_ASSOC);

        $result = $this->database->getCol("SELECT OXUSERID FROM " . self::TABLE_NAME);

        $this->assertInternalType('array', $result);
        $this->assertSame(3, count($result));
        $this->assertSame(array(self::FIXTURE_OXUSERID_1, self::FIXTURE_OXUSERID_2, self::FIXTURE_OXUSERID_3), $result);
    }

    /**
     * Test, that the method 'getCol' works with parameters and a non empty result.
     */
    public function testGetColWithParameters()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getCol("SELECT OXUSERID FROM " . self::TABLE_NAME . " WHERE OXUSERID LIKE ? ", array('%2'));

        $this->assertInternalType('array', $result);
        $this->assertSame(1, count($result));
        $this->assertSame(array(self::FIXTURE_OXUSERID_2), $result);
    }

    /**
     * Test 'getCol' with an INSERT statement
     */
    public function testGetColhWithNonReadStatementThrowsException()
    {
        $expectedExceptionClass = $this->getDatabaseExceptionClassName();

        $this->setExpectedException($expectedExceptionClass);

        $this->database->getCol("INSERT INTO " . self::TABLE_NAME . " VALUES ('a', 'b')");
    }

    /**
     * Test, that a rollback while a transaction cleans up the made changes.
     */
    public function testRollbackTransactionRevertsChanges()
    {
        $exampleOxId = 'XYZ';

        $this->truncateTestTable();
        $this->database->startTransaction();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('$exampleOxId');", array());

        // assure, that the changes are made in this transaction
        $this->assertTestTableHasOnly($exampleOxId);

        $this->database->rollbackTransaction();

        // assure, that the changes are reverted
        $this->assureTestTableIsEmpty();
    }

    /**
     * Test, that the commit of a transaction works as expected.
     */
    public function testCommitTransactionCommitsChanges()
    {
        $exampleOxId = 'XYZ';

        $this->truncateTestTable();
        $this->database->startTransaction();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('$exampleOxId');", array());

        // assure, that the changes are made in this transaction
        $this->assertTestTableHasOnly($exampleOxId);
        $this->database->commitTransaction();

        // assure, that the changes persist the transaction
        $this->assertTestTableHasOnly($exampleOxId);
    }

    /**
     * Test that getAll returns an array with integer keys, if setFetchMode is not called before calling getArray.
     * Test that getAll returns an array with integer keys, if setFetchMode is not called before calling getAll.
     *
     * @todo IMHO This is an inconsistent implementation of ADOdb Lite, as not calling setFetchMode should give the same results
     *       as calling setFetchMode with the param DatabaseInterface::FETCH_MODE_DEFAULT
     *
     * assertSame is not used here as the order of element in the result can crash the test and the order of elements
     * does not matter in this test case.
     */
    public function testGetAllReturnsExpectedResultOnNoFetchModeSet()
    {
        $expectedResult = array(array(self::FIXTURE_OXID_1));
        $message = 'An array with integer keys is returned, if setFetchMode is not called before calling getAll';

        $database = $this->getDb();
        $this->truncateTestTable();
        $database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");

        $actualResult = $database->getAll("SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'");

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that getAll returns an array respecting the given fetch mode.
     * assertSame is not used here as the order of element in the result can crash the test and the order of elements
     * does not matter in this test case.
     *
     * @dataProvider dataProviderTestGetAllRespectsFetchMode
     *
     * @param string $message        Test message
     * @param int    $fetchMode      A given fetch mode
     * @param array  $expectedResult The expected result
     */
    public function testGetAllRespectsTheGivenFetchMode($message, $fetchMode, $expectedResult)
    {
        $this->truncateTestTable();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getAll("SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'");

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * A data provider for the different fetch modes
     *
     * @return array
     */
    public function dataProviderTestGetAllRespectsFetchMode()
    {
        return array(
            [
                'An array with both integer and string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_DEFAULT',
                DatabaseInterface::FETCH_MODE_DEFAULT,
                [[0 => self::FIXTURE_OXID_1, 'OXID' => self::FIXTURE_OXID_1]]
            ],
            array(
                'An array with integer keys is returned for fetch mode DatabaseInterface::FETCH_MODE_NUM',
                DatabaseInterface::FETCH_MODE_NUM,
                array(array(self::FIXTURE_OXID_1))
            ),
            array(
                'An array with string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_ASSOC',
                DatabaseInterface::FETCH_MODE_ASSOC,
                array(array('OXID' => self::FIXTURE_OXID_1))
            ),
            array(
                'An array with both integer and string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_BOTH',
                DatabaseInterface::FETCH_MODE_BOTH,
                array(array(0 => self::FIXTURE_OXID_1, 'OXID' => self::FIXTURE_OXID_1))
            ),
        );
    }

    /**
     * Test that passing parameters to getAll works as expected
     */
    public function testGetAllWithEmptyParameter()
    {
        $message = 'The expected result is returned when passing an empty array as parameter to Doctrine::getAll()';
        $fetchMode = DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(array(self::FIXTURE_OXID_1));

        $this->truncateTestTable();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getAll(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'",
            array()
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that passing parameters to getAll works as expected
     */
    public function testGetAllWithOneParameter()
    {
        $message = 'The expected result is returned when passing an array with one parameter to Doctrine::getAll()';
        $fetchMode = DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(array(self::FIXTURE_OXID_1));

        $this->truncateTestTable();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getAll(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = ?",
            array(self::FIXTURE_OXID_1)
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that passing parameters to getAll works as expected
     */
    public function testGetAllWithMoreThanOneParameters()
    {
        $message = 'The expected result is returned when passing an array with more than one parameter to Doctrine::getAll()';
        $fetchMode = DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(
            array(self::FIXTURE_OXID_1),
            array(self::FIXTURE_OXID_2)
        );

        $this->truncateTestTable();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_2 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getAll(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID IN (?, ?)",
            array(self::FIXTURE_OXID_1, self::FIXTURE_OXID_2)
        );

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that the expected exception is thrown for an invalid query string.
     */
    public function testGetAllThrowsDatabaseExceptionOnInvalidQueryString()
    {
        $expectedExceptionClass = $this->getDatabaseExceptionClassName();

        $this->setExpectedException($expectedExceptionClass);

        $this->database->getAll(
            "SOME INVALID QUERY",
            array()
        );
    }

    /**
     * Provide invalid parameters for getAll.
     * Anything which loosely evaluates to true and is not an array will trigger an exception.
     *
     * @return array
     */
    public function dataProviderTestGetAllThrowsDatabaseExceptionOnInvalidArguments()
    {
        return array(
            array(
                //'Passing a plain string as parameter to getAll triggers an exception',
                'string'
            ),
            array(
                //'Passing an object as parameter to getAll triggers an exception',
                new \stdClass()
            ),
            array(
                //'Passing an integer as parameter to getAll triggers an exception',
                (int) 1
            ),
            array(
                //'Passing a float string as parameter to getAll triggers an exception',
                (float) 1
            ),
            array(
                //'Passing TRUE as parameter to getAll triggers an exception',
                true
            ),
        );
    }

    /**
     * Test that no exception is thrown for a parameter, which loosely evaluates to false.
     * This would be a sign for a logical error in the code and probably trigger a Database error,
     * as the query itself would expect an non empty parameter.
     *
     * See the data provider for arguments, which loosely evaluate to false
     *
     * @dataProvider dataProviderTestGetAllThrowsNoExceptionOnValidArguments
     *
     * @param string $message        An assertion message
     * @param mixed  $validParameter A valid parameter
     */
    public function testGetAllThrowsNoExceptionOnValidArguments($message, $validParameter)
    {
        $fetchMode = DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(array(self::FIXTURE_OXID_1));

        $this->truncateTestTable();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getAll(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'",
            $validParameter
        );

        //$this->truncateTestTable();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Provide invalid parameters for getAll.
     * Anything which loosely evaluates to false will not trigger an exception.
     * Anything which loosely evaluates to true and is an array will not trigger an exception.
     *
     * @return array
     */
    public function dataProviderTestGetAllThrowsNoExceptionOnValidArguments()
    {
        return array(
            array(
                'Passing an empty string as parameter to getAll does not trigger an exception',
                ''
            ),
            array(
                'Passing an null as parameter to getAll does not trigger an exception',
                null
            ),
            array(
                'Passing an empty array as parameter to getAll does not trigger an exception',
                array()
            ),
            array(
                'Passing a false as parameter to getAll does not trigger an exception',
                false
            ),
            array(
                'Passing "0" as parameter to getAll triggers does not trigger an exception',
                "0"
            ),
        );
    }

    /**
     * Test, that the method 'insert_ID' returns 0, if we insert into a table without auto increment.
     */
    public function testInsertIdOnNonAutoIncrement()
    {
        $this->database->execute('INSERT INTO ' . self::TABLE_NAME . ' (OXUSERID) VALUES ("' . self::FIXTURE_OXUSERID_1 . '")');
        $firstInsertedId = $this->database->getLastInsertId();

        $this->assertEquals(0, $firstInsertedId);
    }

    /**
     * Test, that the method 'insert_ID' returns 0, if we don't insert anything at all.
     */
    public function testInsertIdWithoutInsertion()
    {
        $this->database->select('SELECT * FROM ' . self::TABLE_NAME);
        $firstInsertedId = $this->database->getLastInsertId();

        $this->assertEquals(0, $firstInsertedId);
    }

    /**
     * Test, that the method 'insert_ID' leads to correct results, if we insert new rows.
     */
    public function testInsertIdWithInsertion()
    {
        $this->database->execute('CREATE TABLE oxdoctrinetest_autoincrement (oxid INT NOT NULL AUTO_INCREMENT, oxname CHAR, PRIMARY KEY (oxid));');

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $firstInsertedId = $this->database->getLastInsertId();

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $lastInsertedId = $this->database->getLastInsertId();

        $this->database->execute('DROP TABLE oxdoctrinetest_autoincrement;');

        $this->assertEquals(1, $firstInsertedId);
        $this->assertEquals(2, $lastInsertedId);
    }

    /**
     * Test, that the method 'getOne' gives back false, if we try it with an empty table.
     */
    public function testGetOneWithEmptyTable()
    {
        $result = $this->database->getOne('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertFalse($result);
    }

    /**
     * Test, that the method 'getOne' gives back false, if we try it with an invalid sql statement.
     */
    public function testGetOneWithWrongSqlStatement()
    {
        $result = $this->database->getOne('INSERT INTO ' . self::TABLE_NAME . " (oxid) VALUES ('" . self::FIXTURE_OXID_1 . "')");

        $this->assertFalse($result);
    }

    /**
     * Test, that the method 'getOne' gives back the first column of the first row, if we give in a select sql statement
     * with a select all.
     */
    public function testGetOneWithNonEmptyTable()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertEquals(self::FIXTURE_OXID_1, $result);
    }

    /**
     * Test, that the method 'getOne' gives back the column name, if we give in a 'show' statement.
     */
    public function testGetOneWithShowStatement()
    {
        $result = $this->database->getOne('SHOW COLUMNS FROM ' . self::TABLE_NAME);

        $this->assertEquals('oxid', $result);
    }

    /**
     * Test, that the method 'getOne' gives back the correct column of the first row, if we give in the wished sql statement.
     */
    public function testGetOneWithNonEmptyTableAndGivenColumnName()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT OXUSERID FROM ' . self::TABLE_NAME);

        $this->assertEquals(self::FIXTURE_OXUSERID_1, $result);
    }

    /**
     * Test, that the method 'getOne' gives back the correct item, if we give an empty parameters array to it.
     */
    public function testGetOneWithEmptyParameters()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT OXUSERID FROM ' . self::TABLE_NAME, array());

        $this->assertEquals(self::FIXTURE_OXUSERID_1, $result);
    }

    /**
     * Test, that the method 'getOne' gives back the correct item, if we give parameters to it.
     */
    public function testGetOneWithNonEmptyParameters()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getOne('SELECT OXUSERID FROM ' . self::TABLE_NAME . ' WHERE oxid = ?', array(self::FIXTURE_OXID_3));

        $this->assertEquals(self::FIXTURE_OXUSERID_3, $result);
    }


    /**
     * Test, that the method 'getRow' gives an empty array with a non empty table and an incorrect sql statement.
     */
    public function testGetRowIncorrectSqlStatement()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getRow('INSERT INTO ' . self::TABLE_NAME . " (oxid) VALUES ('" . self::FIXTURE_OXID_1 . "')");

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    /**
     * Test, that the method 'getRow' gives an empty array with a non empty table and default fetch mode.
     */
    public function testGetRowNonEmptyTableDefaultFetchMode()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertInternalType('array', $result);
        $this->assertEquals(array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1), $result);
    }

    /**
     * Test, that the method 'getRow' gives back the correct result, when called with parameters.
     */
    public function testGetRowNonEmptyTableWithParameters()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME . ' WHERE oxid = ?', array(self::FIXTURE_OXID_2));

        $this->assertInternalType('array', $result);
        $this->assertEquals(array(self::FIXTURE_OXID_2, self::FIXTURE_OXUSERID_2), $result);
    }

    /**
     * Data provider for getRow.
     * Provides assertion messages and expected results for all possible fetch modes.
     *
     * @return array
     */
    public function dataProviderTestGetRowForAllFetchModes()
    {
        return array(
            array(
                'getRow will return an array with both integer and string keys for DatabaseInterface::FETCH_MODE_DEFAULT',
                DatabaseInterface::FETCH_MODE_DEFAULT,
                array(
                    0          => self::FIXTURE_OXID_1,
                    1          => self::FIXTURE_OXUSERID_1,
                    'oxid'     => self::FIXTURE_OXID_1,
                    'oxuserid' => self::FIXTURE_OXUSERID_1,
                )
            ),
            array(
                'getRow will return an array with integer keys for DatabaseInterface::FETCH_MODE_NUM',
                DatabaseInterface::FETCH_MODE_NUM,
                array(
                    0 => self::FIXTURE_OXID_1,
                    1 => self::FIXTURE_OXUSERID_1,
                )
            ),
            array(
                'getRow will return an array with string keys for DatabaseInterface::FETCH_MODE_ASSOC',
                DatabaseInterface::FETCH_MODE_ASSOC,
                array(
                    'oxid'     => self::FIXTURE_OXID_1,
                    'oxuserid' => self::FIXTURE_OXUSERID_1,
                )
            ),
            array(
                'getRow will return an array with both integer and string keys for DatabaseInterface::FETCH_MODE_BOTH',
                DatabaseInterface::FETCH_MODE_BOTH,
                array(
                    0          => self::FIXTURE_OXID_1,
                    1          => self::FIXTURE_OXUSERID_1,
                    'oxid'     => self::FIXTURE_OXID_1,
                    'oxuserid' => self::FIXTURE_OXUSERID_1,
                )
            ),
        );
    }

    /**
     * Test, that the method 'getRow' returns the expected results for the all possible fetch modes.
     *
     * @dataProvider dataProviderTestGetRowForAllFetchModes
     *
     * @param string $assertionMessage A message explaining the assertion
     * @param int    $fetchMode        The fetch mode we want to test.
     * @param array  $expectedResult   The rows we expect.
     */
    public function testGetRowForAllFetchModes($assertionMessage, $fetchMode, $expectedResult)
    {
        $this->loadFixtureToTestTable();

        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertEquals($expectedResult, $actualResult, $assertionMessage);
    }

    /**
     * Test, that the method 'getRow' gives back the correct result, when called with parameters and consecutive calls.
     */
    public function testGetRowNonEmptyTableWithParametersAndConsecutiveCalls()
    {
        $this->loadFixtureToTestTable();

        $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);
        $result = $this->database->getRow('SELECT * FROM ' . self::TABLE_NAME);

        $this->assertInternalType('array', $result);
        $this->assertEquals(array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1), $result);
    }
    
    public function testCharsetIsNotUtf8WhenUtfModeIsZero()
    {
        $character_set = 'utf8';

        $configFile = Registry::get('oxConfigFile');
        // Store original values
        $savedIUtfMode = $configFile->getVar('iUtfMode');

        $configFile->setVar('iUtfMode', 0);
        $this->resetDbProperty(Database::getInstance());
        $database = Database::getInstance();
        $database->setConfigFile($configFile);
        $databaseConnection = $database::getDb(Database::FETCH_MODE_ASSOC);

        $configFile->setVar('iUtfMode', $savedIUtfMode);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_client\'');
        $this->assertNotEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_client is not ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_results\'');
        $this->assertNotEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_results is not ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_connection\'');
        $this->assertNotEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_client is not ' . $character_set);
    }

    public function testCharsetIsUtf8WhenUtfModeIsOne()
    {
        $character_set = 'utf8';

        $configFile = Registry::get('oxConfigFile');
        // Store original values
        $savedIUtfMode = $configFile->getVar('iUtfMode');
        // Set new values
        $configFile->setVar('iUtfMode', 1);
        $this->resetDbProperty(Database::getInstance());
        $database = Database::getInstance();
        $database->setConfigFile($configFile);
        $databaseConnection = $database::getDb(Database::FETCH_MODE_ASSOC);

        // restore original values
        $configFile->setVar('iUtfMode', $savedIUtfMode);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_client\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 1, character_set_client is ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_results\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 1, character_set_results is ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_connection\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 1, character_set_client is ' . $character_set);
    }

    public function testCharsetMatchesDefaultDatabaseConnectionWhenUtfModeIsZero()
    {
        $character_set = 'cp1251';

        $configFile = Registry::get('oxConfigFile');
        // Store original values
        $savedIUtfMode = $configFile->getVar('iUtfMode');
        $savedCharacterSet = $configFile->getVar('sDefaultDatabaseConnection');
        // Set new values
        $configFile->setVar('iUtfMode', 0);
        $configFile->setVar('sDefaultDatabaseConnection', $character_set);

        $this->resetDbProperty(Database::getInstance());
        $database = Database::getInstance();
        $database->setConfigFile($configFile);
        $databaseConnection = $database::getDb(Database::FETCH_MODE_ASSOC);

        // restore original values
        $configFile->setVar('iUtfMode', $savedIUtfMode);
        $configFile->setVar('sDefaultDatabaseConnection', $savedCharacterSet);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_client\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_client is sDefaultDatabaseConnection: ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_results\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_results is sDefaultDatabaseConnection: ' . $character_set);

        $actualResult = $databaseConnection->getRow('SHOW VARIABLES LIKE \'character_set_connection\'');
        $this->assertEquals($character_set, $actualResult['Value'], 'As \'iUtfMode\' is set to 0, character_set_client is sDefaultDatabaseConnection: ' . $character_set);
    }

    /**
     * Test, that the method 'MetaColumns' works as expected.
     */
    public function testMetaColumns()
    {
        $metaColumnsTestTable = self::TABLE_NAME . '_testmetacolumns';
        $this->createTableForTestMetaColumns($metaColumnsTestTable);
        $columnInformation = $this->database->metaColumns($metaColumnsTestTable);

        $expectedColumns = $this->getExpectedColumnsByTestMetaColumns();

        foreach($expectedColumns as $key => $sub) {
            foreach ($sub as $attributeName => $attributeValue) {
                $this->assertObjectHasAttributeWithValue($columnInformation[$key], $attributeName, $attributeValue);
            }
        }
    }

    /**
     * @dataProvider dataProviderTestQuoteWithValidValues
     *
     * @param mixed $value
     * @param mixed $expectedQuotedValue
     * @param mixed $expectedResult
     * @param string $message
     */
    public function testQuoteWithValidValues($value, $expectedQuotedValue, $expectedResult, $message)
    {
        $this->loadFixtureToTestTable();

        $actualQuotedValue = $this->database->quote($value);

        $this->assertSame($expectedQuotedValue, $actualQuotedValue, $message);

        $query = "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = {$actualQuotedValue}";
        $resultSet = $this->database
            ->select($query);
        $actualResult = $resultSet->getAll();

        $this->assertSame($expectedResult, $actualResult, $message);
    }

    public function dataProviderTestQuoteWithValidValues()
    {
        return [
            [self::FIXTURE_OXID_1, "'" . self::FIXTURE_OXID_1 . "'", [[self::FIXTURE_OXID_1]], 'The string "'. self::FIXTURE_OXID_1 .'" 1  will be converted into the string "\''. self::FIXTURE_OXID_1 .'\'" and the query result will be ['. self::FIXTURE_OXID_1 .']'],
            [1, "'1'", [], 'The integer 1  will be converted into the string "1" and the query result will be empty'],
        ];
    }


    /*
     * There is a another special table needed for testMetaColumns.
     *
     * @param string $metaColumnsTestTable The name of the table to create
     */
    protected function createTableForTestMetaColumns($metaColumnsTestTable)
    {
        $dbh = self::getDatabaseHandler();
        $dbh->exec("CREATE TABLE IF NOT EXISTS " . $metaColumnsTestTable . " (
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
     * specify which results the function metaColumns() expects for each column of the testing table
     */
    protected function getExpectedColumnsByTestMetaColumns()
    {
        return array(
            array(
                'name'           => 'OXINT',
                'max_length'     => '11',
                'type'           => 'int',
                'not_null'       => true,
                'primary_key'    => true,
                'auto_increment' => true,
                'binary'         => false,
                'unsigned'       => false,
                'has_default'    => false,
                'comment'        => 'a column with type INT',
            ),
            array(
                'name'           => 'OXUSERID',
                'max_length'     => '32',
                'type'           => 'char',
                'not_null'       => false,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => false,
                'has_default'    => false,
                'comment'        => 'a column with type CHAR',
                'characterSet'   => 'utf8',
                'collation'      => 'utf8_general_ci'
            ),
            array(
                'name'           => 'OXTIME',
                'type'           => 'time',
                'not_null'       => false,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => false,
                'has_default'    => false,
                'comment'        => 'a column of type TIME',
            ),
            array(
                'name'           => 'OXBIT',
                'max_length'     => '6',
                'type'           => 'bit',
                'not_null'       => true,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => false,
                'comment'        => 'a column with type BIT',
            ),
            array(
                'name'           => 'OXDEC',
                'max_length'     => '6',
                'type'           => 'decimal',
                'not_null'       => true,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => true,
                'has_default'    => true,
                'default_value'  => '1.30',
                'scale'          => '2',
                'comment'        => 'a column with type DECIMAL',
            ),
            array(
                'name'           => 'OXTEXT',
                'type'           => 'text',
                'not_null'       => true,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => false,
                'has_default'    => false,
                'comment'        => 'a column with type TEXT',
                'characterSet'   => 'utf8',
                'collation'      => 'utf8_general_ci'
            ),
            array(
                'name'           => 'OXID',
                'max_length'     => '32',
                'type'           => 'char',
                'not_null'       => true,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => false,
                'has_default'    => false,
                'comment'        => 'a column with type CHAR',
                'characterSet'   => 'utf8',
                'collation'      => 'utf8_general_ci'
            ),
            array(
                'name'           => 'OXBLOB',
                'type'           => 'blob',
                'not_null'       => false,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => true,
                'unsigned'       => false,
                'comment'        => 'a column with type BLOB',
            ),
            array(
                'name'           => 'OXFLOAT',
                'max_length'     => '5',
                'scale'          => '2',
                'type'           => 'float',
                'not_null'       => true,
                'primary_key'    => false,
                'auto_increment' => false,
                'binary'         => false,
                'unsigned'       => true,
                'has_default'    => true,
                'default_value'  => '1.30',
            )
        );
    }

    /**
     * Fetch the transaction isolation level.
     *
     * @return string The transaction isolation level.
     */
    protected function fetchTransactionIsolationLevel()
    {
        $sql = "SELECT VARIABLE_VALUE FROM information_schema.session_variables WHERE variable_name = 'tx_isolation';";

        $resultSet = $this->database->select($sql, array(), false);

        return str_replace('-', ' ', $resultSet->fields[0]);
    }

    /**
     * Helper methods used in this class only
     */

    /**
     * Assure, that the given result set is empty.
     *
     * @param object $resultSet The result set we want to be empty.
     */
    protected function assertEmptyResultSet($resultSet)
    {
        $this->assertTrue($resultSet->EOF);
        $this->assertEmpty($resultSet->fields);

        $this->assertSame($this->getEmptyResultSetClassName(), get_class($resultSet));
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

        $this->assertSame($oxId, $oxIds[0][0]);
    }

    /**
     * Assert, that the table oxdoctrinetest is empty.
     */
    protected function assertTestTableIsEmpty()
    {
        $this->assertTrue($this->isEmptyTestTable());
    }

    /**
     * Fetch all the rows of the oxdoctrinetest table.
     *
     * @return array All rows of the oxdoctrinetest table.
     */
    protected function fetchAllTestTableRows()
    {
        return $this->database
            ->select('SELECT * FROM ' . self::TABLE_NAME, array(), false)
            ->getAll();
    }

    /**
     * Fetch the oxId of the first oxdoctrinetest table row.
     *
     * @return array|false The oxId of the first oxdoctrinetest table row.
     */
    protected function fetchFirstTestTableOxId()
    {
        $rows = $this->database->select('SELECT OXID FROM ' . self::TABLE_NAME, array(), false);
        $row = $rows->fetchRow();

        return $row;
    }

    /**
     * Check, if the table oxdoctrinetest is empty.
     *
     * @return bool Is the table oxdoctrinetest empty?
     */
    protected function isEmptyTestTable()
    {
        return empty($this->fetchAllTestTableRows());
    }

    /**
     * Helper methods to be used in all tests extending this class
     */
    /**
     * Assure, that the table oxdoctrinetest is empty. If it is not empty, the test will fail.
     */
    protected function assureTestTableIsEmpty()
    {
        $this->assertEmpty($this->fetchAllTestTableRows(), "Table '" . self::TABLE_NAME . "' is empty");
    }


    /**
     * Get an instance of ConfigFile based on a empty file.
     *
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }

    public static function resetDbProperty($class) {
        $reflectionClass = new ReflectionClass('OxidEsales\Eshop\Core\Database');

        $reflectionProperty = $reflectionClass->getProperty('db');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, null);
    }
}
