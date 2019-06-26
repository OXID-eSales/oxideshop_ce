<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;

class Object2groupTest extends \OxidTestCase
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
        $oNews = oxNew('oxnews');
        $oNews->oxnews__oxshortdesc = new oxField('Test', oxField::T_RAW);
        $oNews->Save();

        $this->_oGroup = oxNew('oxobject2group');
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
        $this->_oGroup = oxNew('oxobject2group');
        $this->_oGroup->oxobject2group__oxobjectid = new oxField("1111", oxField::T_RAW);
        $this->_oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);

        $this->assertNotNull($this->_oGroup->Save());
    }

    public function testSaveIfAlreadyExists()
    {
        $oGroup = oxNew('oxobject2group');
        $oGroup->oxobject2group__oxobjectid = new oxField($this->_sObjID, oxField::T_RAW);
        $oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);
        $oGroup->Save();

        $oGroup = oxNew('oxobject2group');
        $oGroup->oxobject2group__oxobjectid = new oxField($this->_sObjID, oxField::T_RAW);
        $oGroup->oxobject2group__oxgroupsid = new oxField("oxidnewcustomer", oxField::T_RAW);

        $this->assertNull($oGroup->Save());
    }
}
