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

class Unit_Core_oxGbEntryTest extends OxidTestCase
{

    private $_oObj = null;

    private $_sObjTime = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $myConfig = modConfig::getInstance();
        $this->_oObj = new oxGBEntry();
        $this->_oObj->oxgbentries__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcontent = new oxField("test content\ntest content", oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcreate = new oxField(null, oxField::T_RAW);
        $this->_oObj->oxgbentries__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $this->_oObj->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oObj->delete();
        $this->cleanUpTable('oxgbentries');
        parent::tearDown();
    }

    public function testInsert()
    {
        $iTime = time();

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        oxRegistry::get("oxUtilsDate")->UNITSetTime($iTime);

        $this->_oObj->delete();

        // resaving
        $this->_oObj->oxgbentries__oxcreate = new oxField(null, oxField::T_RAW);
        $this->_oObj->save();

        $this->assertEquals(date('Y-m-d H:i:s', $iTime), $this->_oObj->oxgbentries__oxcreate->value);
    }

    public function testUpdate()
    {
        // copying
        $sBefore = $this->_oObj->oxgbentries__oxcreate->value;

        $this->_oObj->save();

        // comparing
        $this->assertEquals($sBefore, $this->_oObj->oxgbentries__oxcreate->value);
    }

    public function testUpdateWithSpecChar()
    {
        $this->_oObj->oxgbentries__oxcontent = new oxField("test content\ntest content <br>", oxField::T_RAW);
        $this->_oObj->save();

        // comparing
        $this->assertEquals("test content\ntest content <br>", $this->_oObj->oxgbentries__oxcontent->value);

    }

    public function testAssignNoUserData()
    {
        $oObj = new oxGBEntry();
        $oObj->load($this->_oObj->getId());
        $oObj->oxgbentries__oxuserid = new oxField('', oxField::T_RAW);
        $oObj->save();

        $oObj = new oxGBEntry();
        $oObj->load($this->_oObj->getId());
        $this->assertEquals("test content\ntest content", $oObj->oxgbentries__oxcontent->value);
        $this->assertFalse(isset($oObj->oxuser__oxfname));
    }

    public function testAssignWithUserData()
    {
        $oObj = new oxGBEntry();
        $oObj->load($this->_oObj->getId());

        $this->assertEquals("test content\ntest content", $oObj->oxgbentries__oxcontent->value);
        $this->assertTrue(isset($oObj->oxuser__oxfname));
        $this->assertEquals("John", $oObj->oxuser__oxfname->value);
    }

    public function testGetAllEntries()
    {
        $myDB = oxDb::getDb();
        $sSql = 'insert into oxgbentries (oxid,oxshopid,oxuserid,oxcontent)values("_test","' . oxRegistry::getConfig()->getBaseShopId() . '","oxdefaultadmin","AA test content")';
        $myDB->execute($sSql);
        $oObj = new oxGBEntry();
        $aEntries = $oObj->getAllEntries(0, 10, 'oxcontent');
        $this->assertEquals(2, $aEntries->count());
        $oEntry = $aEntries->current();
        $this->assertEquals("AA test content", $oEntry->oxgbentries__oxcontent->value);
    }

    public function testGetAllEntriesModerationOn()
    {
        modConfig::getInstance()->setConfigParam('blGBModerate', 1);
        $myDB = oxDb::getDb();
        $sSql = 'insert into oxgbentries (oxid,oxshopid,oxuserid,oxcontent)values("_test","' . oxRegistry::getConfig()->getBaseShopId() . '","oxdefaultadmin","AA test content")';
        $myDB->execute($sSql);
        $oObj = new oxGBEntry();
        $aEntries = $oObj->getAllEntries(0, 10, null);
        $this->assertEquals(0, $aEntries->count());
        $sSql = 'update oxgbentries set oxactive="1" where oxid="_test"';
        $myDB->execute($sSql);
        $aEntries = $oObj->getAllEntries(0, 10, null);
        $this->assertEquals(1, $aEntries->count());
    }

    public function testGetEntryCount()
    {
        $oObj = new oxGBEntry();
        $iCnt = $oObj->getEntryCount();
        $this->assertEquals(1, $iCnt);
    }

    public function testGetEntryCountModerationOn()
    {
        modConfig::getInstance()->setConfigParam('blGBModerate', 1);
        $oObj = new oxGBEntry();
        $iCnt = $oObj->getEntryCount();
        $this->assertEquals(0, $iCnt);
        $this->_oObj->oxgbentries__oxactive = new oxField(1, oxField::T_RAW);
        $this->_oObj->save();
        $iCnt = $oObj->getEntryCount();
        $this->assertEquals(1, $iCnt);
    }

    public function testFloodProtectionIfAllow()
    {
        $oObj = new oxGBEntry();
        $myConfig = modConfig::getInstance();
        $this->assertFalse($oObj->floodProtection($myConfig->getShopId(), 'oxdefaultadmin'));
    }

    public function testFloodProtectionMaxReached()
    {
        $oObj = new oxGBEntry();
        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam('iMaxGBEntriesPerDay', 1);
        $this->assertTrue($oObj->floodProtection($myConfig->getShopId(), 'oxdefaultadmin'));
    }

    public function testFloodProtectionIfShopAndUserNotSet()
    {
        $oObj = new oxGBEntry();
        $this->assertTrue($oObj->floodProtection());
    }


    public function testSetFieldData()
    {
        $oObj = $this->getProxyClass('oxgbentry');
        $oObj->UNITsetFieldData("oxgbentries__oxcontent", "asd< as");
        $this->assertEquals('asd&lt; as', $oObj->oxgbentries__oxcontent->value);
    }

}
