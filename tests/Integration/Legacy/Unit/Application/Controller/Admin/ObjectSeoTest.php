<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Object_Seo class
 */
class ObjectSeoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $sQ = "delete from oxseo where oxobjectid='objectid'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Testing Object_Seo::isEntrySuffixed()
     */
    public function testIsEntrySuffixed()
    {
        $oView = oxNew('Object_Seo');
        $this->assertFalse($oView->isEntrySuffixed());
    }

    /**
     * Testing Object_Seo::isSuffixSupported()
     */
    public function isSuffixSupported()
    {
        $oView = oxNew('Object_Seo');
        $this->assertFalse($oView->isSuffixSupported());
    }

    /**
     * Testing Object_Seo::showCatSelect()
     */
    public function showCatSelect()
    {
        $oView = oxNew('Object_Seo');
        $this->assertFalse($oView->showCatSelect());
    }

    /**
     * Testing Object_Seo::processParam( $sParam )
     */
    public function testProcessParam()
    {
        $sParam = "param";

        $oView = oxNew('Object_Seo');
        $this->assertSame($sParam, $oView->processParam($sParam));
    }

    /**
     * Testing Object_Seo::getEncoder()
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Object_Seo');
        $this->assertNull($oView->getEncoder());
    }

    /**
     * Testing Object_Seo::getEntryUri()
     */
    public function testGetEntryUri()
    {
        $oView = oxNew('Object_Seo');
        $this->assertNull($oView->getEntryUri());
    }

    /**
     * Testing Object_Seo::getType()
     */
    public function testGetType()
    {
        $oView = oxNew('Object_Seo');
        $this->assertNull($oView->getType());
    }

    /**
     * Testing Object_Seo::getStdUrl()
     */
    public function testGetStdUrl()
    {
        $oView = oxNew('Object_Seo');
        $this->assertNull($oView->getStdUrl("anyid"));
    }

    /**
     * Object_Seo::GetEditLang() test case
     */
    public function testGetEditLang()
    {
        // testing..
        $oView = $this->getProxyClass("Object_Seo");
        $oView->setNonPublicVar("_iEditLang", 999);
        $this->assertSame(999, $oView->getEditLang());
    }

    /**
     * Testing Object_Seo::getAltSeoEntryId()
     */
    public function testGetAltSeoEntryId()
    {
        $oView = oxNew('Object_Seo');
        $this->assertNull($oView->getAltSeoEntryId());
    }

    /**
     * Returns seo entry type
     *
     * @return string
     */
    public function testGetSeoEntryType()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo::class, ["getType"]);
        $oView->expects($this->once())->method('getType')->willReturn("testType");
        $this->assertSame("testType", $oView->getSeoEntryType());
    }

    /**
     * Object_Seo::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Object_Seo');
        $this->assertSame('object_seo', $oView->render());
    }

    /**
     * Object_Seo::Save() test case
     */
    public function testSave()
    {
        $this->setRequestParameter("aSeoData", ["oxseourl" => "testSeoUrl", "oxkeywords" => " testKeywords ", "oxdescription" => " testDescription ", "oxparams" => "testParams", "oxfixed" => 0]);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Core\SeoEncoder::class, ["addSeoEntry"]);
        $oEncoder->expects($this->once())->method("addSeoEntry")->with(
            "objectId",
            1,
            1,
            "stdUrl",
            "testSeoUrl",
            "seoEntryType",
            0,
            "testKeywords",
            "testDescription",
            "param",
            true,
            "altSeoEntryId"
        );

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopId"]);
        $oConfig->expects($this->once())->method("getShopId")->willReturn(1);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo::class, ["getEditObjectId", "getConfig", "getEncoder", "getEditLang", "getStdUrl", "getSeoEntryType", "processParam", "getAltSeoEntryId"], [], '', false);
        $oView->expects($this->once())->method('getEditObjectId')->willReturn("objectId");
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->expects($this->once())->method('getEncoder')->willReturn($oEncoder);
        $oView->expects($this->once())->method('getStdUrl')->willReturn("stdUrl");
        $oView->expects($this->once())->method('getEditLang')->willReturn(1);
        $oView->expects($this->once())->method('getSeoEntryType')->willReturn("seoEntryType");
        $oView->expects($this->once())->method('processParam')->willReturn("param");
        $oView->expects($this->once())->method('getAltSeoEntryId')->willReturn("altSeoEntryId");
        $oView->save();
    }

    /**
     * Object_Seo::getEntryMetaData() test case
     */
    public function testGetEntryMetaData()
    {
        $oEncoder = $this->getMock(\OxidEsales\Eshop\Core\SeoEncoder::class, ["getMetaData"]);
        $oEncoder->expects($this->once())->method('getMetaData')->with(1, "MetaType", "shopid", 1)->willReturn("metaData");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopId"]);
        $oConfig->expects($this->once())->method('getShopId')->willReturn("shopid");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo::class, ["getEncoder", "getEditObjectId", "getConfig", "getEditLang"], [], '', false);
        $oView->expects($this->once())->method('getEncoder')->willReturn($oEncoder);
        $oView->expects($this->once())->method('getEditObjectId')->willReturn(1);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->expects($this->once())->method('getEditLang')->willReturn(1);
        $this->assertSame("metaData", $oView->getEntryMetaData("MetaType"));
    }

    /**
     * Object_Seo::getActCatType() test case
     */
    public function testGetActCatType()
    {
        // testing..
        $oView = oxNew('Object_Seo');
        $this->assertFalse($oView->getActCatType());
    }
}
