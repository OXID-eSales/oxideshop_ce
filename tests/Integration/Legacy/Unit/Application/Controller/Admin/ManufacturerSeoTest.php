<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\SeoEncoderManufacturer;
use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Manufacturer_Seo class
 */
class ManufacturerSeoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $sQ = "delete from oxmanufacturers where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Manufacturer_Seo::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Manufacturer_Seo');
        $this->assertSame('object_seo', $oView->render());
    }

    /**
     * Manufacturer_Seo::GetType() test case
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Manufacturer_Seo');
        $this->assertSame('oxmanufacturer', $oView->getType());
    }

    /**
     * Manufacturer_Seo::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxbase', 'load', '{ return true; }');
        oxTestModules::addFunction('oxbase', 'save', '{ return true; }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerSeo::class, ["getEditObjectId"]);
        $oView->expects($this->atLeastOnce())->method('getEditObjectId')->willReturn(123);

        $this->assertNull($oView->save());
    }

    /**
     * Manufacturer_Seo::getEncoder() test case
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Manufacturer_Seo');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\SeoEncoderManufacturer::class, $oView->getEncoder());
    }

    /**
     * Manufacturer_Seo::getEncoder() test case
     */
    public function testIsSuffixSupported()
    {
        $oView = oxNew('Manufacturer_Seo');
        $this->assertTrue($oView->isSuffixSupported());
    }

    /**
     * Manufacturer_Seo::getEncoder() test case
     */
    public function testIsEntrySuffixed()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test1");

        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField(1);
        $oManufacturer->save();

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test2");

        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField(0);
        $oManufacturer->save();


        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerSeo::class, ["getEditObjectId"]);
        $oView
            ->method('getEditObjectId')
            ->willReturnOnConsecutiveCalls(
                '_test1',
                '_test2'
            );
        $this->assertTrue($oView->isEntrySuffixed());
        $this->assertFalse($oView->isEntrySuffixed());
    }

    /**
     * Manufacturer_Seo::getEncoder() test case
     */
    public function testGetEntryUri()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test1");

        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField(0);
        $oManufacturer->save();

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ["getManufacturerUri"]);
        $oEncoder->expects($this->once())->method('getManufacturerUri')->willReturn("ManufacturerUri");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerSeo::class, ["getEditObjectId", "getEncoder"]);
        $oView->expects($this->once())->method('getEditObjectId')->willReturn("_test1");
        $oView->expects($this->once())->method('getEncoder')->willReturn($oEncoder);
        $this->assertSame("ManufacturerUri", $oView->getEntryUri());
    }

    /**
     * Manufacturer_Seo::_getStdUrl() test case
     */
    public function testGetStdUrl()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test1");

        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField(0);
        $oManufacturer->save();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerSeo::class, ["getEditLang"]);
        $oView->expects($this->once())->method('getEditLang')->willReturn(0);

        $this->assertEquals($oManufacturer->getBaseStdLink(0, true, false), $oView->getStdUrl("_test1"));
    }
}
