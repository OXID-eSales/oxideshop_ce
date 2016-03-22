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
use OxidEsales\Eshop\Core\Database\Doctrine;

/**
 * Tests for our database object.
 */
class DoctrineTest extends UnitTestCase
{

    /**
     * @var bool Should this test use the legacy database for the tests?
     */
    protected $useLegacyDatabase = false;

    /**
     * @var Doctrine The doctrine database we want to test in this class.
     */
    protected $database = null;

    public function setUp()
    {
        parent::setUp();

        if ($this->useLegacyDatabase) {
            $this->database = oxDb::getDb();
        } else {
            $this->database = new Doctrine();
        }
    }

    /**
     * Data provider for testSelectLimit.
     *
     * @return array The parameters we give into testSelectLimit.
     */
    public function dataProvider()
    {
        return array(
            array('SELECT OXID FROM oxorderfiles', -1, -1, false, array()),
            array('SELECT OXID FROM oxorderfiles', 5, -1, false, array()),
        );

    }

    /**
     * Test, that the method 'selectLimit' works without parameters and an empty result.
     *
     * @dataProvider dataProvider
     */
    public function testSelectLimit($sql, $limit, $offset, $parameters, $expected)
    {
        $resultSet = $this->database->selectLimit($sql, $limit, $offset, $parameters);
        $result = $resultSet->getAll();

        $this->assertInternalType('array', $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test, that a rollback while a transaction cleans up the made changes.
     */
    public function testTransactionRollbacked()
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
    public function testTransactionCommitted()
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
     * Test, that the method 'execute' works for insert and delete.
     */
    public function testExecuteWithInsertAndDelete()
    {
        $this->assureOrderFileIsEmpty();

        $exampleOxId = 'XYZ';

        $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ('$exampleOxId');");

        $this->assertEquals(1, $this->database->affected_rows());
        $this->assureOrderFileHasOnly($exampleOxId);

        $this->database->execute("DELETE FROM oxorderfiles WHERE OXID = '$exampleOxId';");

        $this->assertEquals(1, $this->database->affected_rows());
        $this->assureOrderFileIsEmpty();
    }

    /**
     * Test, that the methods 'errorNo' and 'errorMsg' work as expected.
     */
    public function testErrorNoAndErrorMsgWithoutPriorError()
    {
        $errorNumber = $this->database->errorNo();
        $errorMessage = $this->database->errorMsg();

        $this->assertEquals(0, $errorNumber);
        $this->assertEquals('', $errorMessage);
    }

    /**
     * Test, that the methods 'errorNo' and 'errorMsg' work as expected.
     */
    public function testErrorNoAndErrorMsgWork()
    {
        try {
            $this->database->execute("INSERT INTO oxorderfiles (OXID) VALUES ;");
        } catch (Exception $exception) {
            $errorNumber = $this->database->errorNo();
            $errorMessage = $this->database->errorMsg();

            $this->assertEquals(1064, $errorNumber);
            $this->assertEquals('You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'\' at line 1', $errorMessage);
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
        $this->assertEquals(array('OXID'), array_keys($row));

        // check numeric array case
        $previousFetchMode = $this->database->setFetchMode(1);
        $row = $this->fetchFirstProductOxId();

        // reset fetch mode to original setting
        $this->database->setFetchMode($previousFetchMode);

        // check result
        $this->assertInternalType('array', $row);
        $this->assertEquals(array(0), array_keys($row));
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

        $this->assertEquals('READ COMMITTED', $transactionIsolationLevel);

        $this->database->setTransactionIsolationLevel($transactionIsolationLevelPre);
        $transactionIsolationLevel = $this->fetchTransactionIsolationLevel();

        $this->assertEquals($transactionIsolationLevelPre, $transactionIsolationLevel);
    }

    /**
     * Test, that the method 'getCol' works without parameters and an empty result.
     */
    public function testGetColWithoutParametersEmptyResult()
    {
        $result = $this->database->getCol("SELECT OXID FROM oxorderfiles;");

        $this->assertInternalType('array', $result);
        $this->assertEquals(0, count($result));
    }

    /**
     * Test, that the method 'getCol' works without parameters and a non empty result.
     */
    public function testGetColWithoutParameters()
    {
        $result = $this->database->getCol("SELECT OXMAPID FROM oxarticles WHERE OXMAPID > 200 AND OXMAPID < 206");

        $this->assertInternalType('array', $result);
        $this->assertEquals(5, count($result));
        $this->assertEquals(array('201', '202', '203', '204', '205'), $result);
    }

    /**
     * Test, that the method 'getCol' works with parameters and a non empty result.
     */
    public function testGetColWithParameters()
    {
        $result = $this->database->getCol("SELECT OXMAPID FROM oxarticles WHERE OXMAPID > ? AND OXMAPID < ?", array(200, 206));

        $this->assertInternalType('array', $result);
        $this->assertEquals(5, count($result));
        $this->assertEquals(array('201', '202', '203', '204', '205'), $result);
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
     */
    private function assureOrderFileHasOnly($oxId)
    {
        $orderFileIds = $this->fetchOrderFilesOxIds();

        $this->assertNotEmpty($orderFileIds);
        $this->assertEquals(1, count($orderFileIds));
        $this->assertArrayHasKey('0', $orderFileIds);
        $this->assertArrayHasKey('OXID', $orderFileIds[0]);

        $this->assertEquals($oxId, $orderFileIds[0]['OXID']);
    }

    /**
     * Fetch the oxIds from the table oxorderfiles.
     *
     * @return array The oxIds of the table oxorderfiles.
     */
    private function fetchOrderFilesOxIds()
    {
        return $this->database->select('SELECT OXID FROM oxorderfiles;')->getAll();
    }

    /**
     * @return array|false
     */
    private function fetchFirstProductOxId()
    {
        $rows = $this->database->select('SELECT OXID FROM oxarticles');
        $row = $rows->fetchRow();

        return $row;
    }

    /**
     * @return mixed
     */
    private function fetchTransactionIsolationLevel()
    {
        $sql = "SELECT * FROM information_schema.session_variables WHERE variable_name = 'tx_isolation';";
        $resultSet = $this->database->select($sql);
        $resultRow = $resultSet->fetchRow();

        return str_replace('-', ' ', $resultRow['VARIABLE_VALUE']);
    }

}
