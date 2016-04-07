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

require_once realpath(dirname(__FILE__)) . '/DoctrineBaseTest.php';

/**
 * Tests for our database object.
 *
 * @group doctrine
 */
class Integration_Core_Database_DoctrineTest extends Integration_Core_Database_DoctrineBaseTest
{

    /**
     * The data provider for the method testSelect.
     *
     * @return array The parameters for the testSelect.
     */
    public function dataProviderTestSelect()
    {
        return array(
            array( // fetch mode default and an empty result
                   false,
                   null,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array()
            ),
            array( // fetch mode default and one column
                   true,
                   null,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1),
                       array(self::FIXTURE_OXID_2),
                       array(self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode default and multiple columns
                   true,
                   null,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXID',
                   array(
                       array(self::FIXTURE_OXID_1, '1'),
                       array(self::FIXTURE_OXID_2, '2'),
                       array(self::FIXTURE_OXID_3, '3')
                   )
            ),
            array( // fetch mode numeric and an empty result
                   false,
                   1,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array()
            ),
            array( // fetch mode numeric and one column
                   true,
                   1,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array(self::FIXTURE_OXID_1),
                       array(self::FIXTURE_OXID_2),
                       array(self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode numeric and multiple columns
                   true,
                   1,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXUSERID',
                   array(
                       array(self::FIXTURE_OXID_1, self::FIXTURE_OXUSERID_1),
                       array(self::FIXTURE_OXID_2, self::FIXTURE_OXUSERID_2),
                       array(self::FIXTURE_OXID_3, self::FIXTURE_OXUSERID_3)
                   )
            ),
            array( // fetch mode associative and an empty result
                   false,
                   2,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array()
            ),
            array( // fetch mode associative and one column
                   true,
                   2,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1),
                       array('OXID' => self::FIXTURE_OXID_2),
                       array('OXID' => self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode associative and multiple columns
                   true,
                   2,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXUSERID',
                   array(
                       array('OXID' => self::FIXTURE_OXID_1, 'OXUSERID' => self::FIXTURE_OXUSERID_1),
                       array('OXID' => self::FIXTURE_OXID_2, 'OXUSERID' => self::FIXTURE_OXUSERID_2),
                       array('OXID' => self::FIXTURE_OXID_3, 'OXUSERID' => self::FIXTURE_OXUSERID_3)
                   )
            ),

            array( // fetch mode both and an empty result
                   false,
                   3,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array()
            ),
            array( // fetch mode both and one column
                   true,
                   3,
                   'SELECT OXID FROM ' . self::TABLE_NAME,
                   array(
                       array('OXID' => self::FIXTURE_OXID_1, 0 => self::FIXTURE_OXID_1),
                       array('OXID' => self::FIXTURE_OXID_2, 0 => self::FIXTURE_OXID_2),
                       array('OXID' => self::FIXTURE_OXID_3, 0 => self::FIXTURE_OXID_3)
                   )
            ),
            array( // fetch mode both and multiple columns
                   true,
                   3,
                   'SELECT OXID, OXUSERID FROM ' . self::TABLE_NAME . ' ORDER BY OXUSERID',
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
     * Test, that the method 'select' works as expected in the cases, given by the corresponding data provider.
     *
     * @dataProvider dataProviderTestSelect
     *
     * @param bool   $loadFixture  Should the fixture be loaded for this test case?
     * @param int    $fetchMode    The fetch mode we want to test.
     * @param string $sql          The query we want to test.
     * @param array  $expectedRows The rows we expect.
     */
    public function testSelect($loadFixture, $fetchMode, $sql, $expectedRows)
    {
        if ($loadFixture) {
            $this->loadFixtureToTestTable();
        }
        if (!is_null($fetchMode)) {
            $this->database->setFetchMode($fetchMode);
        }

        $resultSet = $this->database->select($sql);
        $rows = $resultSet->getAll();

        $this->assertInternalType('array', $rows, 'Expected an array as result!');
        // sometimes the array gets filled in different order, we sort them to be sure, the content is same
        $this->assertSame(sort($expectedRows), sort($rows));
    }

    /**
     * Data provider for testSelectLimit.
     *
     * @return array The parameters we give into testSelectLimit.
     */
    public function dataProviderTestSelectLimit()
    {
        return array(
            array('SELECT OXID FROM ' . self::TABLE_NAME, false, -1, -1, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, false, 5, -1, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, false, -1, 1, false, array()),
            array('SELECT OXID FROM ' . self::TABLE_NAME, true, 1, 0, false, array(
                array(self::FIXTURE_OXID_1)
            )),
            array('SELECT OXID FROM ' . self::TABLE_NAME, true, 1, 1, false, array(
                array(self::FIXTURE_OXID_2)
            )),
            array('SELECT OXID FROM ' . self::TABLE_NAME, true, 2, 1, false, array(
                array(self::FIXTURE_OXID_2),
                array(self::FIXTURE_OXID_3),
            )),
            array('SELECT OXID FROM ' . self::TABLE_NAME, true, 2, 2, false, array(
                array(self::FIXTURE_OXID_3),
            )),
        );
    }

    /**
     * Test, that the method 'selectLimit' works without parameters and an empty result.
     *
     * @dataProvider dataProviderTestSelectLimit
     *
     * @param string $sql            The sql statement we want to execute.
     * @param bool   $loadFixture    Should the test database table fixture be loaded for this test case?
     * @param int    $limit          The sql starting row.
     * @param int    $offset         The number of rows we are interested in.
     * @param array  $parameters     The parameters we want to give into the 'selectLimit' method.
     * @param array  $expectedResult The expected result of the method call.
     */
    public function testSelectLimit($sql, $loadFixture, $limit, $offset, $parameters, $expectedResult)
    {
        if ($loadFixture) {
            $this->loadFixtureToTestTable();
        }
        $resultSet = $this->database->selectLimit($sql, $limit, $offset, $parameters);
        $result = $resultSet->getAll();

        $this->assertInternalType('array', $result);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Test, that the method 'execute' works with an empty result set for the select query.
     */
    public function testExecuteWithEmptyResultSelect()
    {
        $result = $this->database->execute('SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertTrue($result->EOF);
        $this->assertFalse($result->fields);

        $expectedRows = array();
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }

    /**
     * Test, that the method 'execute' works with an empty result set for the select query,
     * whereby the select clause is not on the first char.
     */
    public function testExecuteWithEmptyResultAndSelectNotOnFirstChar()
    {
        $result = $this->database->execute('   SELECT OXID FROM ' . self::TABLE_NAME);

        $this->assertTrue($result->EOF);
        $this->assertFalse($result->fields);

        $expectedRows = array();
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }

    /**
     * Test, that the method 'execute' works with a non empty result set for the select query.
     */
    public function testExecuteWithNonEmptySelect()
    {
        $this->loadFixtureToTestTable();

        $result = $this->database->execute('SELECT OXID FROM ' . self::TABLE_NAME . ' ORDER BY OXID');

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
     * Test, that the method 'execute' works for insert and delete.
     */
    public function testExecuteWithInsertAndDelete()
    {
        $this->assureTestTableIsEmpty();

        $exampleOxId = self::FIXTURE_OXID_1;

        $resultSet = $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('$exampleOxId');");

        $this->assertEmptyResultSet($resultSet);
        $this->assertSame(1, $this->database->affected_rows());
        $this->assertTestTableHasOnly($exampleOxId);

        $resultSet = $this->database->execute("DELETE FROM " . self::TABLE_NAME . " WHERE OXID = '$exampleOxId';");

        $this->assertEmptyResultSet($resultSet);
        $this->assertSame(1, $this->database->affected_rows());
        $this->assertTestTableIsEmpty();
    }

    /**
     * Test, that the methods 'errorNo' and 'errorMsg' work as expected.
     */
    public function testErrorNoAndErrorMsgWithoutPriorError()
    {
        $this->createDatabase();

        $errorNumber = $this->database->errorNo();
        $errorMessage = $this->database->errorMsg();

        $this->assertSame(0, $errorNumber);
        $this->assertSame('', $errorMessage);
    }

    /**
     * Test, that the methods 'errorNo' and 'errorMsg' work as expected.
     */
    public function testErrorNoAndErrorMsgWork()
    {
        try {
            $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ;");

            $this->fail('A mysql syntax error should produce an exception!');
        } catch (Exception $exception) {
            $errorNumber = $this->database->errorNo();
            $errorMessage = $this->database->errorMsg();

            $this->assertSame(1064, $errorNumber);
            $this->assertSame('You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'\' at line 1', $errorMessage);
        }
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
        $previousFetchMode = $this->database->setFetchMode(1);
        $row = $this->fetchFirstTestTableOxId();

        // reset fetch mode to original setting
        $this->database->setFetchMode($previousFetchMode);

        // check result
        $this->assertInternalType('array', $row);
        $this->assertSame(array(0), array_keys($row));
    }

    /**
     * Test, that the set of the transaction isolation level works.
     */
    public function testSetTransactionIsolationLevel()
    {
        $this->markTestSkipped('Cause atm the oxid user has not the rights to set this value!');
        $transactionIsolationLevelPre = $this->fetchTransactionIsolationLevel();

        $this->database->setTransactionIsolationLevel('READ COMMITTED');
        $transactionIsolationLevel = $this->fetchTransactionIsolationLevel();

        $this->assertSame('READ COMMITTED', $transactionIsolationLevel);

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
     * Test, that a rollback while a transaction cleans up the made changes.
     */
    public function testRollbackTransactionRevertsChanges()
    {
        $this->assureTestTableIsEmpty();

        $exampleOxId = 'XYZ';

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

        $this->assureTestTableIsEmpty();
        $this->database->startTransaction();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('$exampleOxId');", array());

        // assure, that the changes are made in this transaction
        $this->assertTestTableHasOnly($exampleOxId);
        $this->database->commitTransaction();

        // assure, that the changes persist the transaction
        $this->assertTestTableHasOnly($exampleOxId);

        // clean up
        $this->cleanTestTable();
    }

    /**
     * Test that getArray returns an array with integer keys, if setFetchMode is not called before calling getArray.
     *
     * @todo IMHO This is an inconsistent implementation of ADOdb Lite, as not calling setFetchMode should give the same results
     *       as calling setFetchMode with the param DatabaseInterface::FETCH_MODE_DEFAULT
     *
     * assertSame is not used here as the order of element in the result can crash the test and the order of elements
     * does not matter in this test case.
     */
    public function testGetArrayReturnsExpectedResultOnNoFetchModeSet()
    {
        $expectedResult = array(array(self::FIXTURE_OXID_1));
        $message = 'An array with integer keys is returned, if setFetchMode is not called before calling getArray';

        $database = $this->getDb();
        self::assureTestTableIsEmpty();
        $database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");

        $actualResult = $database->getArray("SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'");

        self::assureTestTableIsEmpty();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that getArray returns an array respecting the given fetch mode.
     * assertSame is not used here as the order of element in the result can crash the test and the order of elements
     * does not matter in this test case.
     *
     * @dataProvider dataProviderTestGetArrayRespectsFetchMode
     *
     * @param string $message        Test message
     * @param int    $fetchMode      A given fetch mode
     * @param array  $expectedResult The expected result
     */
    public function testGetArrayRespectsTheGivenFetchMode($message, $fetchMode, $expectedResult)
    {
        self::assureTestTableIsEmpty();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getArray("SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'");

        self::assureTestTableIsEmpty();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * A data provider for the different fetch modes
     *
     * @return array
     */
    public function dataProviderTestGetArrayRespectsFetchMode()
    {
        return array(
            array(
                'An array with both integer and string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_DEFAULT',
                \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_DEFAULT,
                array(array(0 => self::FIXTURE_OXID_1, 'OXID' => self::FIXTURE_OXID_1))
            ),
            array(
                'An array with integer keys is returned for fetch mode DatabaseInterface::FETCH_MODE_NUM',
                \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_NUM,
                array(array(self::FIXTURE_OXID_1))
            ),
            array(
                'An array with string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_ASSOC',
                \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_ASSOC,
                array(array('OXID' => self::FIXTURE_OXID_1))
            ),
            array(
                'An array with both integer and string keys is returned for fetch mode DatabaseInterface::FETCH_MODE_BOTH',
                \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_BOTH,
                array(array(0 => self::FIXTURE_OXID_1, 'OXID' => self::FIXTURE_OXID_1))
            ),
        );
    }

    /**
     * Test that passing parameters to getArray works as expected
     */
    public function testGetArrayWithEmptyParameter()
    {
        $message = 'The expected result is returned when passing an empty array as parameter to Doctrine::getArray()';
        $fetchMode = \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(array(self::FIXTURE_OXID_1));

        self::assureTestTableIsEmpty();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getArray(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = '" . self::FIXTURE_OXID_1 . "'",
            array()
        );

        self::assureTestTableIsEmpty();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }


    /**
     * Test that passing parameters to getArray works as expected
     */
    public function testGetArrayWithOneParameter()
    {
        $message = 'The expected result is returned when passing an array with one parameter to Doctrine::getArray()';
        $fetchMode = \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(array(self::FIXTURE_OXID_1));

        self::assureTestTableIsEmpty();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getArray(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID = ?",
            array(self::FIXTURE_OXID_1)
        );

        self::assureTestTableIsEmpty();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test that passing parameters to getArray works as expected
     */
    public function testGetArrayWithMoreThanOneParameters()
    {
        $message = 'The expected result is returned when passing an array with more than one parameter to Doctrine::getArray()';
        $fetchMode = \OxidEsales\Eshop\Core\Database\DatabaseInterface::FETCH_MODE_NUM;
        $expectedResult = array(
            array(self::FIXTURE_OXID_1),
            array(self::FIXTURE_OXID_2)
        );

        self::assureTestTableIsEmpty();
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_1 . "')");
        $this->database->execute("INSERT INTO " . self::TABLE_NAME . " (OXID) VALUES ('" . self::FIXTURE_OXID_2 . "')");
        $this->database->setFetchMode($fetchMode);

        $actualResult = $this->database->getArray(
            "SELECT OXID FROM " . self::TABLE_NAME . " WHERE OXID IN (?, ?)",
            array(self::FIXTURE_OXID_1, self::FIXTURE_OXID_2)
        );

        self::assureTestTableIsEmpty();

        $this->assertEquals($actualResult, $expectedResult, $message);
    }

    /**
     * Test, that the method 'insert_ID' leads to correct results, if we insert into a table without auto increment.
     */
    public function testInsertIdOnNonAutoIncrement()
    {
        $this->database->execute('INSERT INTO ' . self::TABLE_NAME . ' (OXUSERID) VALUES ("' . self::FIXTURE_OXUSERID_1 . '")');
        $firstInsertedId = $this->database->insert_Id();

        $this->assertEquals(0, $firstInsertedId);
    }

    /**
     * Test, that the method 'insert_ID' leads to correct results, if we don't insert anything at all.
     */
    public function testInsertIdWithoutInsertion()
    {
        $this->database->execute('SELECT * FROM ' . self::TABLE_NAME);
        $firstInsertedId = $this->database->insert_Id();

        $this->assertEquals(0, $firstInsertedId);
    }

    /**
     * Test, that the method 'insert_ID' leads to correct results, if we insert new rows.
     */
    public function testInsertIdWithInsertion()
    {
        $this->database->execute('CREATE TABLE oxdoctrinetest_autoincrement (oxid INT NOT NULL AUTO_INCREMENT, oxname CHAR, PRIMARY KEY (oxid));');

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $firstInsertedId = $this->database->insert_Id();

        $this->database->execute('INSERT INTO oxdoctrinetest_autoincrement(oxname) VALUES ("OXID eSales")');
        $lastInsertedId = $this->database->insert_Id();

        $this->database->execute('DROP TABLE oxdoctrinetest_autoincrement;');

        $this->assertEquals(1, $firstInsertedId);
        $this->assertEquals(2, $lastInsertedId);
    }

    /**
     * Test, that the method 'MetaColumns' works as expected.
     */
    public function testMetaColumns()
    {
        $columnInformation = $this->database->metaColumns(self::TABLE_NAME);

        /**
         * We are skipping the doctrine unsupported features AND the hard to fetch information here.
         */
        $expectedColumns = array(
            array(
                'name'           => 'oxid',
                'max_length'     => '10',
                'type'           => 'varchar',
                'not_null'       => false,
                // 'primary_key'    => false,
                // 'auto_increment' => false,
                // 'binary'         => false,
                // 'unsigned'       => false,
                // 'has_default'    => false
                // 'scale' => null,
            ),
            array(
                'name'           => 'oxuserid',
                'max_length'     => '10',
                'type'           => 'varchar',
                'not_null'       => false,
                // 'primary_key'    => false,
                // 'auto_increment' => false,
                // 'binary'         => false,
                // 'unsigned'       => false,
                // 'has_default'    => false
                // 'scale' => null,
            )
        );

        for ($index = 0; $index < 2; $index++) {
            foreach ($expectedColumns[$index] as $attributeName => $attributeValue) {
                $this->assertObjectHasAttributeWithValue($columnInformation[$index], $attributeName, $attributeValue);
            }
        }
    }

    /**
     * Fetch the transaction isolation level.
     *
     * @return string The transaction isolation level.
     */
    private function fetchTransactionIsolationLevel()
    {
        $sql = "SELECT * FROM information_schema.session_variables WHERE variable_name = 'tx_isolation';";
        $resultSet = $this->database->select($sql, array(), false);
        $resultRow = $resultSet->fetchRow();

        return str_replace('-', ' ', $resultRow['VARIABLE_VALUE']);
    }

    /**
     * Assure, that the given result set is empty.
     *
     * @param object $resultSet The result set we want to be empty.
     */
    private function assertEmptyResultSet($resultSet)
    {
        $this->assertTrue($resultSet->EOF);
        $this->assertEmpty($resultSet->fields);

        if (self::USELEGACYDATABASE) {
            $this->assertSame('ADORecordSet_empty', get_class($resultSet));
        } else {
            $this->assertSame('OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet', get_class($resultSet));
        }
    }
}
