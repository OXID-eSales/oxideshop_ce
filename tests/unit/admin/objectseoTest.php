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

/**
 * Tests for Object_Seo class
 */
class Unit_Admin_ObjectSeoTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxseo where oxobjectid='objectid'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Testing Object_Seo::isEntrySuffixed()
     *
     * @return null
     */
    public function testIsEntrySuffixed()
    {
        $oView = new Object_Seo();
        $this->assertFalse($oView->isEntrySuffixed());
    }

    /**
     * Testing Object_Seo::isSuffixSupported()
     *
     * @return null
     */
    public function isSuffixSupported()
    {
        $oView = new Object_Seo();
        $this->assertFalse($oView->isSuffixSupported());
    }

    /**
     * Testing Object_Seo::showCatSelect()
     *
     * @return null
     */
    public function showCatSelect()
    {
        $oView = new Object_Seo();
        $this->assertFalse($oView->showCatSelect());
    }

    /**
     * Testing Object_Seo::processParam( $sParam )
     *
     * @return null
     */
    public function testProcessParam()
    {
        $sParam = "param";

        $oView = new Object_Seo();
        $this->assertEquals($sParam, $oView->processParam($sParam));
    }

    /**
     * Testing Object_Seo::_getEncoder()
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = new Object_Seo();
        $this->assertNull($oView->UNITgetEncoder());
    }

    /**
     * Testing Object_Seo::getEntryUri()
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $oView = new Object_Seo();
        $this->assertNull($oView->getEntryUri());
    }

    /**
     * Testing Object_Seo::_getType()
     *
     * @return null
     */
    public function testGetType()
    {
        $oView = new Object_Seo();
        $this->assertNull($oView->UNITgetType());
    }

    /**
     * Testing Object_Seo::_getStdUrl()
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $oView = new Object_Seo();
        $this->assertNull($oView->UNITgetStdUrl("anyid"));
    }

    /**
     * Object_Seo::GetEditLang() test case
     *
     * @return null
     */
    public function testGetEditLang()
    {
        // testing..
        $oView = $this->getProxyClass("Object_Seo");
        $oView->setNonPublicVar("_iEditLang", 999);
        $this->assertEquals(999, $oView->getEditLang());
    }

    /**
     * Testing Object_Seo::_getAltSeoEntryId()
     *
     * @return null
     */
    public function testGetAltSeoEntryId()
    {
        $oView = new Object_Seo();
        $this->assertNull($oView->UNITgetAltSeoEntryId());
    }

    /**
     * Returns seo entry type
     *
     * @return string
     */
    public function testGetSeoEntryType()
    {

        $oView = $this->getMock("Object_Seo", array("_getType"));
        $oView->expects($this->once())->method('_getType')->will($this->returnValue("testType"));
        $this->assertEquals("testType", $oView->UNITgetSeoEntryType());
    }

    /**
     * Object_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Object_Seo();
        $this->assertEquals('object_seo.tpl', $oView->render());
    }

    /**
     * Object_Seo::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        modConfig::setRequestParameter("aSeoData", array("oxseourl" => "testSeoUrl", "oxkeywords" => " testKeywords ", "oxdescription" => " testDescription ", "oxparams" => "testParams", "oxfixed" => 0));

        $oEncoder = $this->getMock("oxSeoEncoder", array("addSeoEntry"));
        $oEncoder->expects($this->once())->method("addSeoEntry")->with(
            $this->equalTo("objectId"),
            $this->equalTo(1),
            $this->equalTo(1),
            $this->equalTo("stdUrl"),
            $this->equalTo("testSeoUrl"),
            $this->equalTo("seoEntryType"),
            $this->equalTo(0),
            $this->equalTo("testKeywords"),
            $this->equalTo("testDescription"),
            $this->equalTo("param"),
            $this->equalTo(true),
            $this->equalTo("altSeoEntryId")
        );

        $oConfig = $this->getMock("oxConfig", array("getShopId"));
        $oConfig->expects($this->once())->method("getShopId")->will($this->returnValue(1));

        // testing..
        $oView = $this->getMock("Object_Seo", array("getEditObjectId", "getConfig", "_getEncoder", "getEditLang", "_getStdUrl", "_getSeoEntryType", "processParam", "_getAltSeoEntryId"), array(), '', false);
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("objectId"));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->once())->method('_getStdUrl')->will($this->returnValue("stdUrl"));
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(1));
        $oView->expects($this->once())->method('_getSeoEntryType')->will($this->returnValue("seoEntryType"));
        $oView->expects($this->once())->method('processParam')->will($this->returnValue("param"));
        $oView->expects($this->once())->method('_getAltSeoEntryId')->will($this->returnValue("altSeoEntryId"));
        $oView->save();
    }

    /**
     * Object_Seo::getEntryMetaData() test case
     *
     * @return null
     */
    public function testGetEntryMetaData()
    {
        $oEncoder = $this->getMock("oxSeoEncoder", array("getMetaData"));
        $oEncoder->expects($this->once())->method('getMetaData')->with($this->equalTo(1), $this->equalTo("MetaType"), $this->equalTo("shopid"), $this->equalTo(1))->will($this->returnValue("metaData"));

        $oConfig = $this->getMock("oxConfig", array("getShopId"));
        $oConfig->expects($this->once())->method('getShopId')->will($this->returnValue("shopid"));

        $oView = $this->getMock("Object_Seo", array("_getEncoder", "getEditObjectId", "getConfig", "getEditLang"), array(), '', false);
        $oView->expects($this->once())->method('_getEncoder')->will($this->returnValue($oEncoder));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue(1));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(1));
        $this->assertEquals("metaData", $oView->getEntryMetaData("MetaType"));
    }

    /**
     * Object_Seo::isEntryFixed() test case
     *
     * @return null
     */
    public function isEntryFixed()
    {
        $ShopId = oxRegistry::getConfig()->getShopId();
        $iLang = 0;
        $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed ) values
                                 ( 'objectid', 'ident', '{$ShopId}', '{$iLang}', 'stdurl', 'seourl', 'type', 1 )";
        oxDb::getDb()->execute($sQ);


        $oView = $this->getMock("Object_Seo", array("getEditObjectId"));
        $oView->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue("objectid"));
        $oView->expects($this->at(1))->method('getEditObjectId')->will($this->returnValue("notexistingobjectid"));

        $this->assertTrue($oView->isEntryFixed());
        $this->assertFalse($oView->isEntryFixed());
    }

    /**
     * Object_Seo::getActCatType() test case
     *
     * @return null
     */
    public function testGetActCatType()
    {
        // testing..
        $oView = new Object_Seo();
        $this->assertFalse($oView->getActCatType());
    }

}
