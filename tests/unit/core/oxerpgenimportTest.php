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

class Unit_oxerpgenimportTest_oxUtilsServer extends oxUtilsServer
{

    public function getOxCookie($sName = null)
    {
        return true;
    }
}

class Unit_Core_oxErpGenImportTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myDB = oxDb::getDb();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule('Unit_oxerpgenimportTest_oxUtilsServer');
        $this->cleanUpTable('oxuser');
        parent::tearDown();
    }

    /*
     * Test method getInstanceOfType()
     */
    public function testGetInstanceOfType()
    {
        $oImport = new oxErpGenImport();

        try {
            $oType = $oImport->getInstanceOfType('article');
            $this->assertEquals('oxERPType_Article', get_class($oType));
        } catch (Exception $oE) {
            $this->fail();
        }
    }

    /*
     * Test method _setDbLayerVersion()
     */
    public function testSetDbLayerVersion()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $oImport->UNITsetDbLayerVersion();
        $this->assertEquals("2.9.0", oxErpBase::getRequestedVersion());
    }

    /*
     * Test method _modifyData() calls _mapFields()
     */
    public function testModifyData()
    {
        $oImport = $this->getMock('oxErpGenImport', array('_mapFields'));
        $sParm1 = '1';
        $sParm2 = '2';
        $oImport->expects($this->once())->method('_mapFields')->with($this->equalTo($sParm1), $this->equalTo($sParm2));
        $oImport->UNITmodifyData($sParm1, $sParm2);
    }

    /*
     * Test method _mapFields()
     */
    public function testMapFields()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $aValues = array('aa', 'bb', 'cc');
        $aCsvFileFieldsOrder = array('OXID' => 'oxid', 'OXTITLE' => 'oxtitle', 'OXNAME' => 'oxname');
        $oImport->setNonPublicVar('_aCsvFileFieldsOrder', $aCsvFileFieldsOrder);

        $aMapped = array('oxid' => 'aa', 'oxtitle' => 'bb', 'oxname' => 'cc');

        $this->assertEquals($aMapped, $oImport->UNITmapFields($aValues, null));
    }

    /*
     * Test method _mapFields() correctly maps data if there are skipped fields
     * (M:842)
     */
    public function testMapFieldsWithSkippedFields()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $aValues = array('aa', 'bb', 'cc');
        $aCsvFileFieldsOrder = array('OXID' => 'oxid', 'OXTITLE' => '', 'OXNAME' => 'oxname');
        $oImport->setNonPublicVar('_aCsvFileFieldsOrder', $aCsvFileFieldsOrder);

        $aMapped = array('oxid' => 'aa', 'oxname' => 'cc');

        $this->assertEquals($aMapped, $oImport->UNITmapFields($aValues, null));
    }

    /*
     * Test method _mapFields() correctly maps data if there are fields with 'NULL' value
     * (M:1872)
     */
    public function testMapFieldsWithNullFields()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $aValues = array('aa', 'bb', 'NULL');
        $aCsvFileFieldsOrder = array('OXID' => 'oxid', 'OXNAME' => 'oxname', 'OXVAT' => 'oxvat');
        $oImport->setNonPublicVar('_aCsvFileFieldsOrder', $aCsvFileFieldsOrder);

        $aMapped = array('oxid' => 'aa', 'oxname' => 'bb', 'oxvat' => null);

        $this->assertEquals($aMapped, $oImport->UNITmapFields($aValues, null));
    }

    /*
     * Test method _getImportMode()
     */
    public function testGetImportMode()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $this->assertEquals(oxERPBase::$MODE_IMPORT, $oImport->UNITgetImportMode(null));
    }

    /*
     * Test method getImportObject()
     */
    public function testGetImportObject()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $oType = $oImport->getImportObject('A');

        $this->assertEquals('oxERPType_Article', get_class($oType));
    }

    /*
     * Test method setImportTypePrefix()
     */
    public function testSetImportTypePrefix()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $oImport->setImportTypePrefix("A");
        $this->assertEquals('A', $oImport->getNonPublicVar('_sImportTypePrefix'));
    }

    /*
     * Test method setImportTypePrefix()
     */
    public function testSetImportObjectsList()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");

        $aObjects = array(
            "Z" => 'oxaccessoire2article',
            "I" => 'oxactions2article',
            "Y" => 'oxartextends',
            "A" => 'oxarticles',
            "K" => 'oxcategories',
            "N" => 'oxcountry',
            "C" => 'oxobject2article',
            "T" => 'oxobject2category',
            "O" => 'oxorder',
            "R" => 'oxorderarticles',
            "P" => 'oxprice2article',
            "U" => 'oxuser',
            "H" => 'oxvendor'
        );

        $this->assertEquals($aObjects, $oImport->getImportObjectsList());
    }

    /*
     * Test method init()
     */
    public function testInit()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");

        oxAddClassModule('Unit_oxerpgenimportTest_oxUtilsServer', 'oxUtilsServer');
        //logging in
        $oUser = $this->getMock('oxuser', array('isAdmin'));
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oUser->loadAdminUser();

        $this->assertTrue($oImport->init(null, null));
        $this->assertEquals(oxRegistry::getSession()->getId(), $oImport->getNonPublicVar('_sSID'));
        $this->assertTrue($oImport->getNonPublicVar('_blInit'));
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oImport->getNonPublicVar('_iLanguage'));
        $this->assertEquals($oUser->getId(), $oImport->getNonPublicVar('_sUserID'));

    }

    /*
     * Test method init() - with not logged in user
     */
    public function testInitWhenUserIsNotLoggedIn()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");

        try {
            $this->assertTrue($oImport->init(null, null));
            $this->fail('Init must fail with not logged user');
        } catch (Exception $oEx) {
        }
    }

    /*
     * Test method init() - test if init resets imoprt counter
     */
    public function testInitResetsImportCounter()
    {
        $oImport = $this->getMock('oxErpGenImport', array('_resetIdx'));
        $oImport->expects($this->once())->method('_resetIdx');

        oxAddClassModule('Unit_oxerpgenimportTest_oxUtilsServer', 'oxUtilsServer');
        //logging in
        $oUser = $this->getMock('oxuser', array('isAdmin'));
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oUser->login(oxADMIN_LOGIN, oxADMIN_PASSWD);
        $oUser->loadAdminUser();

        $oImport->init(null, null);
    }

    /*
     * Test method setCsvFileFieldsOrder()
     */
    public function testSetCsvFileFieldsOrder()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $aOrder = array('oxid', 'oxtitle', 'oxname');
        $oImport->setCsvFileFieldsOrder($aOrder);
        $this->assertEquals($aOrder, $oImport->getNonPublicVar('_aCsvFileFieldsOrder'));
    }

    /*
     * Test method setCsvContainsHeader()
     */
    public function testSetCsvContainsHeader()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $oImport->setCsvContainsHeader(5);
        $this->assertEquals(5, $oImport->getNonPublicVar('_blCsvContainsHeader'));
    }

    /*
     * Test method getTotalImportedRowsNumber()
     */
    public function testGetTotalImportedRowsNumber()
    {
        $oCsv = new oxErpGenImport();
        $oCsv->setImportedIds(12);
        $oCsv->setImportedIds(12);
        $oCsv->setImportedIds(120);

        $this->assertEquals(2, $oCsv->getTotalImportedRowsNumber());
    }

    /*
     * Test method doImport() - if an exception is thrown when user is not logged in
     */
    public function testDoImportFailsWhenUserIsNotLoggedIn()
    {
        $oImport = $this->getProxyClass("oxErpGenImport");
        $this->assertEquals('ERPGENIMPORT_ERROR_USER_NO_RIGHTS', $oImport->doImport());
    }

    /*
     * Test method doImport() - if fails when bad import file specified
     */
    public function testDoImportFailsWhenImportFileNotFound()
    {
        $oImport = $this->getMock('oxErpGenImport', array('init'));
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));

        $this->assertEquals('ERPGENIMPORT_ERROR_WRONG_FILE', $oImport->doImport('nosuchfile'));
    }

    /*
     * Test method doImport()
     */
    public function testDoImport()
    {
        $oImport = $this->getMock('oxErpGenImport', array('init', '_checkAccess'));
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('_checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(true);
        $oImport->setImportTypePrefix('U');
        $oImport->setCsvFileFieldsOrder(array("OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"));

        $oImport->doImport(getTestsBasePath().'misc/csvWithHeader.csv');

        $aTestData1 = array(array("_testId1", "1", "oxbaseshop", "userName1", "FirstName1", "LastName1"));
        $aTestData2 = array(array("_testId2", "1", "oxbaseshop", "userName2", "FirstName2", "LastName2"));

        $aUser1 = oxDb::getDb()->getAll("select OXID, OXACTIVE, OXSHOPID, OXUSERNAME, OXFNAME, OXLNAME from oxuser where oxid='_testId1'");
        $aUser2 = oxDb::getDb()->getAll("select OXID, OXACTIVE, OXSHOPID, OXUSERNAME, OXFNAME, OXLNAME from oxuser where oxid='_testId2'");

        $this->assertEquals($aTestData1, $aUser1);
        $this->assertEquals($aTestData2, $aUser2);
    }

    /*
     * Test method doImport() - if skips header line
     */
    public function testDoImportSkipsHeaderLine()
    {
        $oImport = $this->getMock('oxErpGenImport', array('init', '_checkAccess'));
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('_checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(true);
        $oImport->setImportTypePrefix('U');
        $oImport->setCsvFileFieldsOrder(array("OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"));

        //checking if header line was not saved to DB
        $oImport->doImport(getTestsBasePath().'misc/csvWithHeader.csv');
        $this->assertEquals(2, count($oImport->getStatistics()));
        $this->assertFalse(oxDb::getDb()->getOne("select OXID from oxuser where oxid='OXID'"));

    }

    /*
     * Test method doImport() - when no header line is in csv file
     */
    public function testDoImportWithCsvWithoutHeaderLine()
    {
        $oImport = $this->getMock('oxErpGenImport', array('init', '_checkAccess'));
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('_checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(false);
        $oImport->setImportTypePrefix('U');
        $oImport->setCsvFileFieldsOrder(array("OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"));

        //checking if first line from csv file was saved to DB
        $oImport->doImport(getTestsBasePath().'misc/csvWithoutHeader.csv');
        $this->assertEquals('_testId1', oxDb::getDb()->getOne("select oxid from oxuser where oxid='_testId1'"));
    }

    /*
     * Test method _getCsvFieldsTerminator()
     */
    public function testGetCsvFieldsTerminator()
    {
        modConfig::getInstance()->setConfigParam('sGiCsvFieldTerminator', "");
        modConfig::getInstance()->setConfigParam('sCSVSign', ",");
        $oImport = $this->getProxyClass("oxErpGenImport");

        $this->assertEquals(",", $oImport->UNITgetCsvFieldsTerminator());

        modConfig::getInstance()->setConfigParam('sGiCsvFieldTerminator', ";");
        modConfig::getInstance()->setConfigParam('sCSVSign', ",");
        $oImport = $this->getProxyClass("oxErpGenImport");

        $this->assertEquals(";", $oImport->UNITgetCsvFieldsTerminator());

    }

    /*
     * Test method _getCsvFieldsTerminator()
     */
    public function testGetCsvFieldsTerminatorDefault()
    {
        modConfig::getInstance()->setConfigParam('sCSVSign', null);
        $oImport = $this->getProxyClass("oxErpGenImport");

        $this->assertEquals(';', $oImport->UNITgetCsvFieldsTerminator());
    }

    /*
     * Test method _getCsvFieldsTerminator()
     */
    public function testGetCsvFieldsEncolser()
    {
        modConfig::getInstance()->setConfigParam('sGiCsvFieldEncloser', "'");
        $oImport = $this->getProxyClass("oxErpGenImport");

        $this->assertEquals("'", $oImport->UNITgetCsvFieldsEncolser());
    }

    /*
     * Test method _getCsvFieldsTerminator()
     */
    public function testGetCsvFieldsEncolserDefault()
    {
        modConfig::getInstance()->setConfigParam('sGiCsvFieldEncloser', null);
        $oImport = $this->getProxyClass("oxErpGenImport");

        $this->assertEquals('"', $oImport->UNITgetCsvFieldsEncolser());
    }
}
