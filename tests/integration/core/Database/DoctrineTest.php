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
    public function dataProvider_testSelect()
    {
        return array(
            array( // fetch mode default and an empty result
                   null,
                   'SELECT OXID FROM oxorderfiles',
                   array()
            ),
            array( // fetch mode default and one column
                   null,
                   'SELECT OXID FROM oxvendor',
                   array(
                       array('9437def212dc37c66f90cc249143510a'),
                       array('d2e44d9b31fcce448.08890330'),
                       array('d2e44d9b32fd2c224.65443178')
                   )
            ),
            array( // fetch mode default and multiple columns
                   null,
                   'SELECT OXID, OXMAPID, OXACTIVE FROM oxvendor ORDER BY OXMAPID',
                   array(
                       array('d2e44d9b31fcce448.08890330', '1', '1'),
                       array('d2e44d9b32fd2c224.65443178', '2', '1'),
                       array('9437def212dc37c66f90cc249143510a', '3', '1')
                   )
            ),

            array( // fetch mode numeric and an empty result
                   1,
                   'SELECT OXID FROM oxorderfiles',
                   array()
            ),
            array( // fetch mode numeric and one column
                   1,
                   'SELECT OXID FROM oxvendor',
                   array(
                       array('9437def212dc37c66f90cc249143510a'),
                       array('d2e44d9b31fcce448.08890330'),
                       array('d2e44d9b32fd2c224.65443178')
                   )
            ),
            array( // fetch mode numeric and multiple columns
                   1,
                   'SELECT OXID, OXMAPID, OXACTIVE FROM oxvendor ORDER BY OXMAPID',
                   array(
                       array('d2e44d9b31fcce448.08890330', '1', '1'),
                       array('d2e44d9b32fd2c224.65443178', '2', '1'),
                       array('9437def212dc37c66f90cc249143510a', '3', '1')
                   )
            ),

            array( // fetch mode associative and an empty result
                   2,
                   'SELECT OXID FROM oxorderfiles',
                   array()
            ),
            array( // fetch mode associative and one column
                   2,
                   'SELECT OXID FROM oxvendor',
                   array(
                       array('OXID' => '9437def212dc37c66f90cc249143510a'),
                       array('OXID' => 'd2e44d9b31fcce448.08890330'),
                       array('OXID' => 'd2e44d9b32fd2c224.65443178')
                   )
            ),
            array( // fetch mode associative and multiple columns
                   2,
                   'SELECT OXID, OXMAPID, OXACTIVE FROM oxvendor ORDER BY OXMAPID',
                   array(
                       array('OXID' => 'd2e44d9b31fcce448.08890330', 'OXMAPID' => '1', 'OXACTIVE' => '1'),
                       array('OXID' => 'd2e44d9b32fd2c224.65443178', 'OXMAPID' => '2', 'OXACTIVE' => '1'),
                       array('OXID' => '9437def212dc37c66f90cc249143510a', 'OXMAPID' => '3', 'OXACTIVE' => '1')
                   )
            ),

            array( // fetch mode both and an empty result
                   3,
                   'SELECT OXID FROM oxorderfiles',
                   array()
            ),
            array( // fetch mode both and one column
                   3,
                   'SELECT OXID FROM oxvendor',
                   array(
                       array('OXID' => '9437def212dc37c66f90cc249143510a', 0 => '9437def212dc37c66f90cc249143510a'),
                       array('OXID' => 'd2e44d9b31fcce448.08890330', 0 => 'd2e44d9b31fcce448.08890330'),
                       array('OXID' => 'd2e44d9b32fd2c224.65443178', 0 => 'd2e44d9b32fd2c224.65443178')
                   )
            ),
            array( // fetch mode both and multiple columns
                   3,
                   'SELECT OXID, OXMAPID, OXACTIVE FROM oxvendor ORDER BY OXMAPID',
                   array(
                       array(
                           'OXID'     => 'd2e44d9b31fcce448.08890330',
                           'OXMAPID'  => '1',
                           'OXACTIVE' => '1',
                           0          => 'd2e44d9b31fcce448.08890330',
                           1          => '1',
                           2          => '1'
                       ),
                       array(
                           'OXID'     => 'd2e44d9b32fd2c224.65443178',
                           'OXMAPID'  => '2',
                           'OXACTIVE' => '1',
                           0          => 'd2e44d9b32fd2c224.65443178',
                           1          => '2',
                           2          => '1'
                       ),
                       array(
                           'OXID'     => '9437def212dc37c66f90cc249143510a',
                           'OXMAPID'  => '3',
                           'OXACTIVE' => '1',
                           0          => '9437def212dc37c66f90cc249143510a',
                           1          => '3',
                           2          => '1'
                       )
                   )
            ),

        );
    }

    /**
     * Test, that the method 'select' works as expected in the cases, given by the corresponding data provider.
     *
     * @dataProvider dataProvider_testSelect
     *
     * @param int    $fetchMode    The fetch mode we want to test.
     * @param string $sql          The query we want to test.
     * @param array  $expectedRows The rows we expect.
     */
    public function testSelect($fetchMode, $sql, $expectedRows)
    {
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
    public function dataProvider_testSelectLimit()
    {
        return array(
            array('SELECT OXID FROM oxorderfiles', -1, -1, false, array()),
            array('SELECT OXID FROM oxorderfiles', 5, -1, false, array()),
            array('SELECT OXID FROM oxorderfiles', -1, 1, false, array()),
            array('SELECT OXID FROM oxvendor', 1, 0, false, array(
                array('9437def212dc37c66f90cc249143510a')
            )),
            array('SELECT OXID FROM oxvendor', 1, 1, false, array(
                array('d2e44d9b31fcce448.08890330')
            )),
            array('SELECT OXID FROM oxvendor', 2, 1, false, array(
                array('d2e44d9b31fcce448.08890330'),
                array('d2e44d9b32fd2c224.65443178'),
            )),
            array('SELECT OXID FROM oxvendor', 2, 2, false, array(
                array('d2e44d9b32fd2c224.65443178'),
            )),
        );
    }

    /**
     * Test, that the method 'selectLimit' works without parameters and an empty result.
     *
     * @dataProvider dataProvider_testSelectLimit
     *
     * @param string $sql            The sql statement we want to execute.
     * @param int    $limit          The sql starting row.
     * @param int    $offset         The number of rows we are interested in.
     * @param array  $parameters     The parameters we want to give into the 'selectLimit' method.
     * @param array  $expectedResult The expected result of the method call.
     */
    public function testSelectLimit($sql, $limit, $offset, $parameters, $expectedResult)
    {
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
        $result = $this->database->execute('SELECT OXID FROM oxorderfiles');

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
        $result = $this->database->execute('   SELECT OXID FROM oxorderfiles');

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
        $result = $this->database->execute('SELECT OXID FROM oxvendor ORDER BY OXID');

        $this->assertFalse($result->EOF);
        $this->assertSame(array('9437def212dc37c66f90cc249143510a'), $result->fields);

        $expectedRows = array(
            array("9437def212dc37c66f90cc249143510a"),
            array("d2e44d9b31fcce448.08890330"),
            array("d2e44d9b32fd2c224.65443178")
        );
        $allRows = $result->getAll();
        $this->assertSame($expectedRows, $allRows);
    }

    /**
     * Test, that the method 'execute' works for insert and delete.
     */
    public function testExecuteWithInsertAndDelete()
    {
        $this->assureOrderFileIsEmpty();

        $exampleOxId = 'XYZ';

        $resultSet = $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ('$exampleOxId');");

        $this->assertEmptyResultSet($resultSet);
        $this->assertSame(1, $this->database->affected_rows());
        $this->assureOrderFileHasOnly($exampleOxId);

        $resultSet = $this->database->execute("DELETE FROM oxorderfiles WHERE OXID = '$exampleOxId';");

        $this->assertEmptyResultSet($resultSet);
        $this->assertSame(1, $this->database->affected_rows());
        $this->assureOrderFileIsEmpty();
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
            $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ;");

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
        // check normal (associative array) case
        $row = $this->fetchFirstProductOxId();
        $this->assertInternalType('array', $row);
        $this->assertSame(array(0), array_keys($row));

        // check numeric array case
        $previousFetchMode = $this->database->setFetchMode(1);
        $row = $this->fetchFirstProductOxId();

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
        $result = $this->database->getCol("SELECT OXID FROM oxorderfiles;");

        $this->assertInternalType('array', $result);
        $this->assertSame(0, count($result));
    }

    /**
     * Test, that the method 'getCol' works without parameters and a non empty result.
     */
    public function testGetColWithoutParameters()
    {
        $result = $this->database->getCol("SELECT OXMAPID FROM oxarticles WHERE OXMAPID > 200 AND OXMAPID < 206");

        $this->assertInternalType('array', $result);
        $this->assertSame(5, count($result));
        $this->assertSame(array('201', '202', '203', '204', '205'), $result);
    }

    /**
     * Test, that the method 'getCol' works with parameters and a non empty result.
     */
    public function testGetColWithParameters()
    {
        $result = $this->database->getCol("SELECT OXMAPID FROM oxarticles WHERE OXMAPID > ? AND OXMAPID < ?", array(200, 206));

        $this->assertInternalType('array', $result);
        $this->assertSame(5, count($result));
        $this->assertSame(array('201', '202', '203', '204', '205'), $result);
    }

    /**
     * Test, that a rollback while a transaction cleans up the made changes.
     */
    public function testRollbackTransactionRevertsChanges()
    {
        $this->assureOrderFileIsEmpty();

        $exampleOxId = 'XYZ';

        $this->database->startTransaction();
        $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ('$exampleOxId');", array());

        // assure, that the changes are made in this transaction
        $this->assureOrderFileHasOnly($exampleOxId);

        $this->database->rollbackTransaction();

        // assure, that the changes are reverted
        $this->assureOrderFileIsEmpty();
    }

    /**
     * Test, that the commit of a transaction works as expected.
     */
    public function testCommitTransactionCommitsChanges()
    {
        $exampleOxId = 'XYZ';

        $this->deleteOrderFilesEntry($exampleOxId);

        $this->assureOrderFileIsEmpty();
        $this->database->startTransaction();
        $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ('$exampleOxId');", array());

        // assure, that the changes are made in this transaction
        $this->assureOrderFileHasOnly($exampleOxId);
        $this->database->commitTransaction();

        // assure, that the changes persist the transaction
        $this->assureOrderFileHasOnly($exampleOxId);

        // clean up
        $this->deleteOrderFilesEntry($exampleOxId);
    }

    /**
     * Delete an entry from the database table oxorderfiles.
     *
     * @param string $oxId The oxId of the row to delete.
     */
    protected function deleteOrderFilesEntry($oxId)
    {
        $this->database->execute("DELETE FROM oxorderfiles WHERE OXID = '$oxId';");
    }

    /**
     * Assure, that the table oxorderfiles is empty.
     */
    private function assureOrderFileIsEmpty()
    {
        $orderFileIds = $this->fetchOrderFilesOxIds();

        $this->assertEmpty($orderFileIds);
    }

    /**
     * Assure, that the table oxorderfiles has only the given oxId.
     *
     * @param string $oxId The oxId we want to be the only one in the oxorderfile table.
     */
    private function assureOrderFileHasOnly($oxId)
    {
        $orderFileIds = $this->fetchOrderFilesOxIds();

        $this->assertNotEmpty($orderFileIds);
        $this->assertSame(1, count($orderFileIds));
        $this->assertArrayHasKey('0', $orderFileIds);

        $this->assertSame($oxId, $orderFileIds[0][0]);
    }

    /**
     * Fetch the oxIds from the table oxorderfiles.
     *
     * @return array The oxIds of the table oxorderfiles.
     */
    private function fetchOrderFilesOxIds()
    {
        return $this->database->select('SELECT OXID FROM oxorderfiles;', array(), false)->getAll();
    }

    /**
     * Fetch the oxId of the first product.
     *
     * @return array|false The oxId of the first product.
     */
    private function fetchFirstProductOxId()
    {
        $rows = $this->database->select('SELECT OXID FROM oxarticles', array(), false);
        $row = $rows->fetchRow();

        return $row;
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

        if ($this->useLegacyDatabase) {
            $this->assertSame('ADORecordSet_empty', get_class($resultSet));
        } else {
            $this->assertSame('OxidEsales\Eshop\Core\Database\DoctrineEmptyResultSet', get_class($resultSet));
        }
    }

}
