<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \stdClass;
use \oxDb;

/**
 * Testing oxattribute class.
 */
class AttributeTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oAttr = oxNew('oxAttribute');
        $this->_oAttr->oxattribute__oxtitle = new oxField("test", oxField::T_RAW);
        $this->_oAttr->save();

        // article attribute
        $oNewGroup = oxNew('oxbase');
        $oNewGroup->Init('oxobject2attribute');
        $oNewGroup->oxobject2attribute__oxobjectid = new oxField("test_oxid", oxField::T_RAW);
        $oNewGroup->oxobject2attribute__oxattrid = new oxField($this->_oAttr->getId(), oxField::T_RAW);
        $oNewGroup->oxobject2attribute__oxvalue = new oxField("testvalue", oxField::T_RAW);
        $oNewGroup->Save();

        // category attribute
        $oNewGroup = oxNew('oxbase');
        $oNewGroup->Init('oxcategory2attribute');
        $oNewGroup->oxcategory2attribute__oxobjectid = new oxField("test_oxid", oxField::T_RAW);
        $oNewGroup->oxcategory2attribute__oxattrid = new oxField($this->_oAttr->getId(), oxField::T_RAW);
        $oNewGroup->Save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oAttr->delete();
        parent::tearDown();
    }

    /**
     * Test delete non existing attribute.
     *
     * @return null
     */
    public function testDeleteNonExisting()
    {
        $oAttr = oxNew('oxAttribute');
        $this->assertFalse($oAttr->delete());
    }

    /**
     * Test delete existing attribute.
     *
     * @return null
     */
    public function testDeleteExisting()
    {
        $this->_oAttr->delete();

        $sCheckOxid1 = oxDb::getDb()->GetOne("select oxid from oxobject2attribute where oxattrid = '{$this->sOxid}'");
        $sCheckOxid2 = oxDb::getDb()->GetOne("select oxid from oxcategory2attribute where oxattrid = '{$this->sOxid}'");
        $oAttr = oxNew('oxAttribute');
        if ($sCheckOxid1 || $sCheckOxid2 || $oAttr->Load($this->_oAttr->getId())) {
            $this->fail("fail deleting");
        }
    }

    /**
     * Test assign variables to attribute.
     *
     * @return null
     */
    public function testAssignVarToAttribute()
    {
        $myDB = oxDb::getDB();
        $oAttr = oxNew("oxAttribute");
        $sVarId = '_testVar';
        $sVarId2 = '_testVar2';
        $aSellTitle = array(0 => '_testAttr',
                            1 => '_tetsAttr_1');
        $oValue = new stdClass();
        $oValue->name = 'red';
        $oValue2 = new stdClass();
        $oValue2->name = 'rot';
        $oValue3 = new stdClass();
        $oValue3->name = 'blue';
        $oValue4 = new stdClass();
        $oValue4->name = 'blau';
        $aSellValue = array($sVarId  => array(0 => $oValue,
                                              1 => $oValue2),
                            $sVarId2 => array(0 => $oValue3,
                                              1 => $oValue4));
        $oAttr->assignVarToAttribute($aSellValue, $aSellTitle);
        $this->assertEquals(2, $myDB->getOne("select count(*) from oxobject2attribute where oxobjectid like '_testVar%'"));
        $oRez = $myDB->select("select oxvalue, oxvalue_1, oxobjectid  from oxobject2attribute where oxobjectid = '_testVar'");
        while (!$oRez->EOF) {
            $oRez->fields = array_change_key_case($oRez->fields, CASE_LOWER);
            $this->assertEquals('red', $oRez->fields[0]);
            $this->assertEquals('_testVar', $oRez->fields[2]);
            $this->assertEquals('rot', $oRez->fields[1]);
            $oRez->fetchRow();
        }
    }

    /**
     * Test get attribute id.
     *
     * @return null
     */
    public function testGetAttrId()
    {
        $oAttr = $this->getProxyClass("oxAttribute");
        $this->assertTrue((bool) $oAttr->UNITgetAttrId('Design'));
        $this->assertFalse((bool) $oAttr->UNITgetAttrId('aaaaa'));
    }

    /**
     * Test create attribute.
     *
     * @return null
     */
    public function testCreateAttribute()
    {
        $oAttr = $this->getProxyClass("oxAttribute");
        $aSellTitle = array(0 => '_testAttr',
                            1 => '_testAttr_1');
        $sId = $oAttr->UNITcreateAttribute($aSellTitle);
        $this->assertEquals('_testAttr', oxDb::getDB()->getOne("select oxtitle from oxattribute where oxid = '$sId'"));
        $this->assertEquals('_testAttr_1', oxDb::getDB()->getOne("select oxtitle_1 from oxattribute where oxid = '$sId'"));
    }

    /**
     * Test get attribute assigns.
     *
     * @return null
     */
    public function testGetAttributeAssigns()
    {
        $oAttr = $this->getProxyClass("oxAttribute");
        $aId = $oAttr->getAttributeAssigns('test_oxid');
        $this->assertEquals(1, count($aId));
    }


    /**
     * Test set attribute title.
     *
     * @return null
     */
    public function testSetTitle()
    {
        $oAttr = oxNew('oxAttribute');
        $oAttr->setTitle('title');
        $this->assertEquals('title', $oAttr->getTitle());
    }

    /**
     * Test set attribute active value.
     *
     * @return null
     */
    public function testSetActiveValue()
    {
        $oAttr = oxNew('oxAttribute');
        $oAttr->setActiveValue('selectedValue');
        $this->assertEquals('selectedValue', $oAttr->getActiveValue());
    }

    /**
     * Test add attribute value.
     *
     * @return null
     */
    public function testAddValue()
    {
        $oAttr = oxNew('oxAttribute');
        $oAttr->addValue('val1');
        $oAttr->addValue('val2');

        $this->assertEquals(array('val1', 'val2'), $oAttr->getValues());
    }
}
