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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxLegacyDbTest extends OxidTestCase
{

    /**
     * Test case for seting connection
     *
     * @return null
     */
    public function testSetConnection()
    {
        $oLegacyDb = new oxLegacyDb();

        $oConnection = 'someConection';

        $oLegacyDb->setConnection($oConnection);
        $this->assertNotNull($oLegacyDb->getDb(false));

    }



    /**
     * Test case for oxLegacyDb::execute();
     */
    public function testExecute()
    {
        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->execute('query');
    }


    /**
     * Test case for oxLegacyDb::getOne();
     */
    public function testGetOne()
    {
        $oDbLib = $this->getMock("dbLib", array("getOne"));
        $oDbLib->expects($this->once())->method('getOne')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getOne('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::getArray();
     */
    public function testGetArray()
    {
        $oDbLib = $this->getMock("dbLib", array("getArray"));
        $oDbLib->expects($this->once())->method('getArray')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getArray('query', false, false);
    }


    /**
     * Test case for oxLegacyDb::getRow();
     */
    public function testGetRow()
    {
        $oDbLib = $this->getMock("dbLib", array("getRow"));
        $oDbLib->expects($this->once())->method('getRow')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getRow('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::getAll();
     */
    public function testGetAll()
    {
        $oDbLib = $this->getMock("dbLib", array("getAll"));
        $oDbLib->expects($this->once())->method('getAll')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getAll('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::select();
     */
    public function testSelect()
    {
        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->select('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::getAssoc();
     */
    public function testGetAssoc()
    {
        $oDbLib = $this->getMock("dbLib", array("getAssoc"));
        $oDbLib->expects($this->once())->method('getAssoc')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getAssoc('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::getCol();
     */
    public function testGetCol()
    {
        $oDbLib = $this->getMock("dbLib", array("getCol"));
        $oDbLib->expects($this->once())->method('getCol')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->getCol('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::selectLimit();
     */
    public function testSelectLimit()
    {
        $oDbLib = $this->getMock("dbLib", array("SelectLimit"));
        $oDbLib->expects($this->once())->method('SelectLimit')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->selectLimit('query', -1, -1, false, false);
    }

    /**
     * Test case for oxLegacyDb::query();
     */
    public function testQuery()
    {
        $oDbLib = $this->getMock("dbLib", array("Query"));
        $oDbLib->expects($this->once())->method('Query')->with($this->equalTo('query'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->query('query', false, false);
    }

    /**
     * Test case for oxLegacyDb::Affected_Rows();
     */
    public function testAffected_Rows()
    {
        $oDbLib = $this->getMock("dbLib", array("Affected_Rows"));
        $oDbLib->expects($this->once())->method('Affected_Rows');

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->Affected_Rows();
    }

    /**
     * Test case for oxLegacyDb::ErrorNo();
     */
    public function testErrorNo()
    {
        $oDbLib = $this->getMock("dbLib", array("ErrorNo"));
        $oDbLib->expects($this->once())->method('ErrorNo');

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->ErrorNo();
    }

    /**
     * Test case for oxLegacyDb::errorMsg();
     */
    public function testerrorMsg()
    {
        $oDbLib = $this->getMock("dbLib", array("errorMsg"));
        $oDbLib->expects($this->once())->method('errorMsg');

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->errorMsg();
    }

    /**
     * Test case for oxLegacyDb::quote();
     */
    public function testQuote()
    {
        $oDbLib = $this->getMock("dbLib", array("quote"));
        $oDbLib->expects($this->once())->method('quote')->with($this->equalTo('value'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->will($this->returnValue($oDbLib));

        $oLegacyDb->quote('value');
    }

    /**
     * Test case for oxLegacyDb::qstr();
     */
    public function testqstr()
    {
        $oDbLib = $this->getMock("dbLib", array("qstr"));
        $oDbLib->expects($this->once())->method('qstr')->with($this->equalTo('value'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->will($this->returnValue($oDbLib));

        $oLegacyDb->qstr('value');
    }

    /**
     * Test case for oxLegacyDb::metaColumns();
     */
    public function testMetaColumns()
    {
        $oDbLib = $this->getMock("dbLib", array("MetaColumns"));
        $oDbLib->expects($this->once())->method('MetaColumns')->with($this->equalTo('value'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->will($this->returnValue($oDbLib));

        $oLegacyDb->metaColumns('value');
    }

    /**
     * Test case for oxLegacyDb::metaColumnNames();
     */
    public function testMetaColumnNames()
    {
        $oDbLib = $this->getMock("dbLib", array("MetaColumnNames"));
        $oDbLib->expects($this->once())->method('MetaColumnNames')->with($this->equalTo('value'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->will($this->returnValue($oDbLib));

        $oLegacyDb->metaColumnNames('value');
    }

    /**
     * Test case for oxLegacyDb::startTransaction();
     */
    public function testStartTransaction()
    {
        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('START TRANSACTION'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->startTransaction();
    }

    /**
     * Test case for oxLegacyDb::commitTransaction();
     */
    public function testCommitTransaction()
    {
        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('COMMIT'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->commitTransaction();
    }

    /**
     * Test case for oxLegacyDb::rollbackTransaction();
     */
    public function testRollbackTransaction()
    {
        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('ROLLBACK'));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->rollbackTransaction();
    }

    /**
     * Test case for oxLegacyDb::setTransactionIsolationLevel();
     */
    public function testSetTransactionIsolationLevel()
    {
        $sLevel = 'READ COMMITTED';

        $oDbLib = $this->getMock("dbLib", array("execute"));
        $oDbLib->expects($this->once())->method('execute')->with($this->equalTo('SET TRANSACTION ISOLATION LEVEL ' . $sLevel));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->setTransactionIsolationLevel($sLevel);
    }

    /**
     * Test case for oxLegacyDb::setTransactionIsolationLevel();
     */
    public function testSetTransactionIsolationLevelBadName()
    {
        $sLevel = 'Bad level';

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->never())->method("getDb");

        $oLegacyDb->setTransactionIsolationLevel($sLevel);
    }

    /**
     * Test case for oxLegacyDb::UI();
     */
    public function testUI()
    {
        $iParam = 10;
        $oDbLib = $this->getMock("dbLib", array("UI"));
        $oDbLib->expects($this->once())->method('UI')->with($this->equalTo($iParam));

        $oLegacyDb = $this->getMock("oxLegacyDb", array("getDb"));
        $oLegacyDb->expects($this->once())->method('getDb')->with($this->equalTo(false))->will($this->returnValue($oDbLib));

        $oLegacyDb->UI($iParam);
    }

    /**
     * Test, that the method forceMasterConnection is implemented.
     */
    public function testForceMasterConnectionIsImplemented()
    {
        $oLegacyDb = oxNew('oxLegacyDb');

        $oLegacyDb->forceMasterConnection();
    }

}
