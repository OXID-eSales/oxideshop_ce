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

class Unit_Core_oxobject2groupTest extends OxidTestCase
{

    private $_oGroup = null;
    private $_sObjID = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oNews = new oxnews();
        $oNews->oxnews__oxshortdesc = new oxField('Test', oxField::T_RAW);
        $oNews->Save();

        $this->_oGroup = new oxobject2group();
        $this->_oGroup->oxobject2group__oxobjectid = new oxField($oNews->getId(), oxField::T_RAW);
        $this->_oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);
        $this->_oGroup->Save();

        $this->_sObjID = $oNews->getId();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb();
        $sDelete = "delete from oxnews where oxid='" . $this->_sObjID . "'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxobject2group where oxobjectid='" . $this->_sObjID . "'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxobject2group where oxobjectid='1111'";
        $oDB->Execute($sDelete);

        parent::tearDown();
    }

    public function testSave()
    {
        $sSelect = "select 1 from oxobject2group where oxobjectid='{$this->_sObjID}'";

        $this->assertEquals('1', oxDb::getDb()->getOne($sSelect));
    }

    public function testSaveNew()
    {
        $this->_oGroup = new oxobject2group();
        $this->_oGroup->oxobject2group__oxobjectid = new oxField("1111", oxField::T_RAW);
        $this->_oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);

        $this->assertNotNull($this->_oGroup->Save());
    }

    public function testSaveIfAlreadyExists()
    {
        $oGroup = new oxobject2group();
        $oGroup->oxobject2group__oxobjectid = new oxField($this->_sObjID, oxField::T_RAW);
        $oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);
        $oGroup->Save();

        $oGroup = new oxobject2group();
        $oGroup->oxobject2group__oxobjectid = new oxField($this->_sObjID, oxField::T_RAW);
        $oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);

        $this->assertNull($oGroup->Save());
    }
}
