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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use Exception;
use oxConfigFile;
use oxDb;
use OxidEsales\Eshop\Core\exception\DatabaseConnectionException;
use oxRegistry;
use OxidEsales\Eshop\Core\ShopIdCalculator;

/**
 * Test private methods with mock.
 * oxDb do not extend oxSuperConfig so do not have magic getter for private methods.
 *
 * @group database-adapter
 */
class oxDbPublicized extends oxDb
{
    public function getConfigParam($sConfigName)
    {
        return parent::getConfigParam($sConfigName);
    }

    public function onConnectionError(DatabaseConnectionException $exception)
    {
        parent::onConnectionError($exception);
    }

    public function notifyConnectionErrors(Exception $exception)
    {
        parent::notifyConnectionErrors($exception);
    }

    public function _setUp($connection)
    {
        return parent::_setUp($connection);
    }

    public function _getModules()
    {
        return parent::_getModules();
    }

    public static function cleanTblCache()
    {
        oxDb::$_aTblDescCache = array();
    }

    public static function clearInstance()
    {
        oxDb::$_oDB = null;
    }
}

class DbTest extends \OxidTestCase
{
    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');

        $oDb = oxDb::getInstance();
        $oDb->setConfig(oxRegistry::get('oxConfigFile'));

        parent::tearDown();
    }

    public function testSetConfig()
    {
        $iDebug = 7;

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = $iDebug;

        $oDb = new oxDbPublicized();
        $oDb->setConfig($oConfigFile);
        $this->assertEquals($iDebug, $oDb->getConfigParam('_iDebug'));

        $iDebug = 8;
        $oConfigFile->iDebug = $iDebug;
        $oDb->setConfig($oConfigFile);

        $this->assertEquals($iDebug, $oDb->getConfigParam('_iDebug'), 'Debug should be same as set in setConfig()');
    }

    public function testSetDbObject()
    {
        $oxDb = oxNew('oxDb');
        $dbMock = $this->getDbObjectMock();

        $oxDb->setDbObject($dbMock);

        $realResult = $oxDb->getDb();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testGetDbObject()
    {
        $oxDb = oxNew('oxDb');
        $dbMock = $this->getDbObjectMock();

        $oxDb->setDbObject($dbMock);

        $realResult = $oxDb->getDbObject();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testQuoteArray()
    {
        $oDb = oxNew('oxDb');

        $aArray = array("asd'", 'pppp');
        $aRezArray = array("'asd\''", "'pppp'");
        $this->assertEquals($aRezArray, $oDb->quoteArray($aArray));
    }

    public function testGetTableDescription()
    {
        oxDbPublicized::cleanTblCache();

        $rs = oxDb::getDb()->execute("show tables");
        $icount = 3;
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF && $icount--) {
                $sTable = $rs->fields[0];

                $amc = oxDb::getDb()->MetaColumns($sTable);
                $rmc1 = oxDb::getInstance()->GetTableDescription($sTable);
                $rmc2 = oxDb::getInstance()->GetTableDescription($sTable);

                $this->assertEquals($amc, $rmc1, "not cached return is bad [shouldn't be] of $sTable.");
                $this->assertEquals($amc, $rmc2, "cached [simple] return is bad of $sTable.");

                $rs->MoveNext();
            }
        } else {
            $this->fail("no tables???");
        }
    }

    public function testIsValidFieldName()
    {
        $oDb = oxNew('oxDb');
        $this->assertTrue($oDb->isValidFieldName('oxid'));
        $this->assertTrue($oDb->isValidFieldName('oxid_1'));
        $this->assertTrue($oDb->isValidFieldName('oxid.1'));
        $this->assertFalse($oDb->isValidFieldName('oxid{1'));
    }

    /**
     * Testing escaping string
     */
    public function testEscapeString()
    {
        $sString = "\x00 \n \r ' \, \" \x1a";

        $oDb = oxDb::getInstance();

        $this->assertEquals('\0 \n \r \\\' \\\, \" \Z', $oDb->escapeString($sString));

    }

    public function testGetDb()
    {
        oxDbPublicized::clearInstance();
        $oDb = oxNew("oxDb");

        $this->assertTrue($oDb instanceof oxDb);
        $this->assertEquals('testRes', $oDb->getDb()->getOne("SELECT 'testRes'"));
    }

    public function testGetDbFetchMode()
    {
        $oDb = oxNew("oxDb");

        //unfortunately we should use globals in order to test this behaviour
        global $ADODB_FETCH_MODE;

        $oDb->getDb();
        $this->assertEquals($ADODB_FETCH_MODE, ADODB_FETCH_NUM);

        $oDb->getDb(true);
        $this->assertEquals($ADODB_FETCH_MODE, ADODB_FETCH_ASSOC);

        $oDb->getDb(oxDb::FETCH_MODE_ASSOC);
        $this->assertEquals($ADODB_FETCH_MODE, ADODB_FETCH_ASSOC);

        $oDb->getDb(oxDb::FETCH_MODE_NUM);
        $this->assertEquals($ADODB_FETCH_MODE, ADODB_FETCH_NUM);
    }

    /**
     * Test case for oxDb::startTransaction(), oxDb::commitTransaction() and
     * oxDb::rollbackTransaction()
     */
    public function testTransactions()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sQ1 = "INSERT INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXORDERINFO`, `OXPIXIEXPORT`, `OXPIXIEXPORTED`, `OXVPE`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`) VALUES
                    ('_testArtId', '1', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0802-85-823-7-1', '', '', '', '', '', 109, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2010-03-02 17:09:35', 0, 0, 0, '', '', '', '', 0, 0, '', 0, 0, 'W 32/L 30 | Blau', 0, '', 'W 32/L 30 | Blue ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 12001, 0, 0, 0, 0, 0, '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0)";
        } else {
            $sQ1 = "INSERT INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
                    ('_testArtId', ".ShopIdCalculator::BASE_SHOP_ID.", '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0802-85-823-7-1', '', '', '', '', '', 109, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2010-03-02 20:07:21', 0, 0, 0, '', '', '', '', 0, 0, '', 0, 0, 'W 32/L 30 | Blau', 0, '', 'W 32/L 30 | Blue ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 12001, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, '')";
        }

        $sQ2 = 'select 1 from oxarticles where oxid = "_testArtId"';
        $sQ3 = 'delete from oxarticles where oxid = "_testArtId"';

        $oDb = oxNew("oxDb");
        $oDbInst = $oDb->getDb();

        /** Test 1 **/ // commiting transaction
        $oDbInst->startTransaction();
        $oDbInst->execute($sQ1);
        $oDbInst->commitTransaction();

        // testing
        $this->assertTrue((bool) $oDbInst->getOne($sQ2));

        // deleting
        $oDbInst->execute($sQ3);

        /** Test 2 **/ // rollbacking transaction
        $oDbInst->startTransaction();
        $oDbInst->execute($sQ1);
        $oDbInst->rollbackTransaction();

        // testing
        $this->assertFalse((bool) $oDbInst->getOne($sQ2));
    }

    /**
     * Test case for oxDb::_getModules()
     */
    public function testGetModules()
    {
        // admin logging + debug level = 7
        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = 7;
        $oConfigFile->isAdmin = true;
        $oConfigFile->blLogChangesInAdmin = true;

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("isAdmin"));
        $oDb->setConfig($oConfigFile);
        $oDb->expects($this->once())->method("isAdmin")->will($this->returnValue(true));
        $this->assertEquals("perfmon:oxadminlog", $oDb->_getModules());

        // debug level = 0
        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = 0;
        $oConfigFile->isAdmin = false;

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("getConfig"));
        $oDb->setConfig($oConfigFile);
        $this->assertEquals("", $oDb->_getModules());
    }

    /**
     * Tests whether addodb exceptions throwing is enabled
     */
    public function testGetModules_AddoDbExceptionHandlerSet()
    {
        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = 0;
        $oConfigFile->isAdmin = false;

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("getConfig"));
        $oDb->setConfig($oConfigFile);
        $oDb->_getModules();

        $this->assertTrue(defined('ADODB_ERROR_HANDLER'));
        $this->assertEquals("adodb_throw", ADODB_ERROR_HANDLER);

        global $ADODB_EXCEPTION;
        $this->assertEquals('oxAdoDbException', $ADODB_EXCEPTION);
    }

    /**
     * Test case for oxDb::_setUp()
     */
    public function testSetUp_UTF()
    {
        $oDbInst = $this->getMock("oxdb", array("execute", "logSQL"));
        $oDbInst->expects($this->at(0))->method('execute')->with($this->equalTo('truncate table adodb_logsql'));
        $oDbInst->expects($this->at(1))->method('logSQL')->with($this->equalTo(true));
        $oDbInst->expects($this->at(2))->method('execute')->with($this->equalTo('SET @@session.sql_mode = ""'));
        $oDbInst->expects($this->at(3))->method('execute')->with($this->equalTo('SET NAMES "utf8"'));
        $oDbInst->expects($this->at(4))->method('execute')->with($this->equalTo('SET CHARACTER SET utf8'));
        $oDbInst->expects($this->at(5))->method('execute')->with($this->equalTo('SET CHARACTER_SET_CONNECTION = utf8'));
        $oDbInst->expects($this->at(6))->method('execute')->with($this->equalTo('SET CHARACTER_SET_DATABASE = utf8'));
        $oDbInst->expects($this->at(7))->method('execute')->with($this->equalTo('SET character_set_results = utf8'));
        $oDbInst->expects($this->at(8))->method('execute')->with($this->equalTo('SET character_set_server = utf8'));

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = 7;
        $oConfigFile->iUtfMode = true;

        $oDb = new oxDbPublicized();
        $oDb->setConfig($oConfigFile);
        $oDb->_setUp($oDbInst);
    }

    /**
     * Test case for oxDb::_setUp()
     */
    public function testSetUp_nonUTF()
    {
        // non-UTF
        $oDbInst = $this->getMock("oxDb", array("execute", "logSQL"));
        $oDbInst->expects($this->at(0))->method('execute')->with($this->equalTo('truncate table adodb_logsql'));
        $oDbInst->expects($this->at(1))->method('logSQL')->with($this->equalTo(true));
        $oDbInst->expects($this->at(2))->method('execute')->with($this->equalTo('SET @@session.sql_mode = ""'));
        $oDbInst->expects($this->at(3))->method('execute')->with($this->equalTo('SET NAMES "nonutf"'));

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->iDebug = 7;
        $oConfigFile->iUtfMode = false;
        $oConfigFile->sDefaultDatabaseConnection = "nonutf";

        $oDb = new oxDbPublicized();
        $oDb->setConfig($oConfigFile);
        $oDb->_setUp($oDbInst);
    }

    /**
     * Test case for oxDb::notifyConnectionErrors()
     *
     * @expectedException oxConnectionException
     */
    public function testNotifyConnectionErrors()
    {
        $oDbInst = $this->getMock("oxDb", array("errorMsg", "errorNo"));
        $oDbInst->expects($this->never())->method('errorMsg');
        $oDbInst->expects($this->never())->method('errorNo');

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->sAdminEmail = "adminemail";
        $oConfigFile->dbUser = "dbuser";

        $exception = oxNew('Exception');

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("getConfig", "_sendMail"));
        $oDb->setConfig($oConfigFile);
        $oDb->expects($this->once())->method('_sendMail')->with($this->equalTo('adminemail'), $this->equalTo('Offline warning!'));

        $this->setExpectedException('oxConnectionException');
        $oDb->notifyConnectionErrors($exception);
    }

    /**
     * Test case for oxDb::onConnectionError()
     */
    public function testOnConnectionError()
    {
        $exception = oxNew('Exception');
        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("notifyConnectionErrors"));
        $oDb->expects($this->once())->method('notifyConnectionErrors')->with($this->equalTo($exception));
        $oDb->onConnectionError($exception);
    }

    /**
     * Cleans provided query
     *
     * @param string $query
     *
     * @return string
     */
    protected function cleanSQL($query)
    {
        return preg_replace(array('/[^\w\'\:\-\.\*]/'), '', $query);
    }

    /**
     * @return oxConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new oxConfigFile($this->createFile('config.inc.php', '<?php '));
    }
}
