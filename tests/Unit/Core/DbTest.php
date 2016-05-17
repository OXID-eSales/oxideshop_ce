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
use oxDb;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;
use OxidEsales\Eshop\Core\exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\ShopIdCalculator;

/**
 * @group database-adapter
 */

/**
 * Class DbTest
 * TODO rename to DatabaseTest
 *
 * @covers OxidEsales\Eshop\Core\Database
 * @package Unit\Core
 */
class DbTest extends UnitTestCase
{
    protected function setUp() {
        parent::setUp();

        $database = Database::getInstance();
        $database->setConfigFile(Registry::get('oxConfigFile'));
    }

    /**
     * Clean-up oxarticles table + parent::tearDown()
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    public function testSetConfig()
    {
        $debug = 7;

        $configFile = $this->getBlankConfigFile();
        $configFile->iDebug = $debug;

        $database = Database::getInstance();
        $database->setConfigFile($configFile);
        $methodGetConfigParam = self::getReflectedMethod('getConfigParam');

        $actualResult = $methodGetConfigParam->invokeArgs($database, array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');

        $debug = 8;
        $configFile->iDebug = $debug;
        $database->setConfigFile($configFile);
        $methodGetConfigParam = self::getReflectedMethod('getConfigParam');

        $actualResult = $methodGetConfigParam->invokeArgs($database, array('iDebug'));

        $this->assertEquals($debug, $actualResult, 'Result of getConfigParam(iDebug) should match value in config.inc.php');
    }

    public function testSetDbObject()
    {
        $database = Database::getInstance();
        $dbMock = $this->getDbObjectMock();

        $database->setDbObject($dbMock);

        $realResult = $database->getDb();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testGetDbObject()
    {
        $database = Database::getInstance();
        $dbMock = $this->getDbObjectMock();

        $database->setDbObject($dbMock);

        $realResult = $database->getDbObject();
        $this->assertEquals($dbMock, $realResult);
    }

    public function testGetTableDescription()
    {
        self::callMethod('resetTblDescCache');

        $rs = Database::getDb()->execute("show tables");
        $icount = 3;
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF && $icount--) {
                $sTable = $rs->fields[0];

                $amc = Database::getDb()->metaColumns($sTable);
                $rmc1 = Database::getInstance()->getTableDescription($sTable);
                $rmc2 = Database::getInstance()->getTableDescription($sTable);

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
        $database = Database::getInstance();

        $this->assertTrue($database->isValidFieldName('oxid'));
        $this->assertTrue($database->isValidFieldName('oxid_1'));
        $this->assertTrue($database->isValidFieldName('oxid.1'));
        $this->assertFalse($database->isValidFieldName('oxid{1'));
    }

    /**
     * Testing escaping string
     * Todo Remove when deprecated in 5.3
     */
    public function testEscapeString()
    {
        $sString = "\x00 \n \r ' \, \" \x1a";

        $database = Database::getInstance();

        $this->assertEquals('\0 \n \r \\\' \\\, \" \Z', $database->escapeString($sString));

    }

    public function testGetInstanceReturnsInstanceOfDatabase()
    {
        $database = Database::getInstance();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database', $database);
    }

    public function testGetDbReturnsAnInstanceOfDatabaseInterface()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\DatabaseInterface', $database);
    }

    public function testGetDbReturnsAnInstanceOfDoctrine()
    {
        $database = Database::getDb();

        $this->assertInstanceOf('OxidEsales\Eshop\Core\Database\Doctrine', $database);
    }

    /**
     *
     */
    public function testOnPostConnectIsCalled()
    {
        $this->markTestSkipped('Figure out how to test that onPostConnect has been called');
        $databaseMock = $this->getMockBuilder('OxidEsales\Eshop\Core\Database')
            ->setMethods(['onPostConnect'])
            ->getMock();

        $databaseMock->expects($this->once())->method('onPostConnect');

        $this->resetDbProperty($databaseMock);
        $databaseMock::getDb();
    }

    /**
     * TODO Remove this test
     */
    public function testGetDbFetchMode()
    {
        $this->markTestSkipped('This kind of global fetch mode saving is not supported by the new doctine dbal database adapter.');
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
     *
     * TODO Remove completely or move to tests/Integration
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
     * Test case for oxDb::notifyConnectionErrors()
     *
     * TODO Move this test to integration tests
     */
    public function testNotifyConnectionErrors()
    {
        // TODO Put this in PHPDoc block again: @expectedException DatabaseConnectionException

        $this->markTestSkipped('Move this test to integration tests');

        $oDbInst = $this->getMock("oxDb", array("errorMsg", "errorNo"));
        $oDbInst->expects($this->never())->method('errorMsg');
        $oDbInst->expects($this->never())->method('errorNo');

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->sAdminEmail = "adminemail";
        $oConfigFile->dbUser = "dbuser";

        $exception = oxNew('Exception');

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("getConfig", "sendMail"));
        $oDb->setConfig($oConfigFile);
        $oDb->expects($this->once())->method('sendMail')->with($this->equalTo('adminemail'), $this->equalTo('Offline warning!'));

        $this->setExpectedException('OxidEsales\Eshop\Core\exception\DatabaseException');
        $oDb->notifyConnectionErrors($exception);
    }

    /**
     * Test case for oxDb::onConnectionError()
     *
     * TODO Move this test to integration tests
     */
    public function testOnConnectionError()
    {
        $this->markTestSkipped('Move this test to integration tests');

        $exception = oxNew('OxidEsales\Eshop\Core\exception\DatabaseConnectionException', 'THE CONNECTION ERROR MESSAGE!', 42, new \Exception());

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array('notifyConnectionErrors', 'redirectToMaintenancePage'));
        $oDb->expects($this->once())->method('notifyConnectionErrors')->with($this->equalTo($exception));
        $oDb->expects($this->once())->method('redirectToMaintenancePage');

        $oDb->onConnectionError($exception);
    }

    /**
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }

    /**
     * @param $methodName
     * @param $params
     */
    protected static function callMethod($methodName, array $params = array())
    {
        $class = new Database();
        $reflectedMethod = self::getReflectedMethod($methodName);

        return $reflectedMethod->invokeArgs($class, $params);
    }

    /**
     * Helper method for accessing protected class methods
     *
     * @param string $name Name of the protected method
     *
     * @return mixed The reflected method
     */
    protected static function getReflectedMethod($name)
    {
        $class = new ReflectionClass('OxidEsales\Eshop\Core\Database');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public static function resetDbProperty($class) {
        $reflectionClass = new ReflectionClass('OxidEsales\Eshop\Core\Database');

        $reflectionProperty = $reflectionClass->getProperty('db');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, null);

    }
}
