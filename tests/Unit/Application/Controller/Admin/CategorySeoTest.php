<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxDb;
use OxidEsales\EshopCommunity\Application\Model\SeoEncoderCategory;
use \oxTestModules;

/**
 * Tests for Category_Seo class
 */
class CategorySeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxcategories where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        parent::tearDown();
    }

    /**
     * Category_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Category_Seo');
        $this->assertEquals('object_seo.tpl', $oView->render());
    }

    /**
     * Category_Seo::GetType() test case
     *
     * @return null
     */
    public function testGetType()
    {
        // testing..
        $oView = oxNew('Category_Seo');
        $this->assertEquals('oxcategory', $oView->UNITgetType());
    }

    /**
     * Category_Seo::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcategory', 'load', '{ return true; }');
        oxTestModules::addFunction('oxcategory', 'save', '{ return true; }');
        oxTestModules::addFunction('oxSeoEncoderCategory', 'markRelatedAsExpired', '{ throw new Exception( "markRelatedAsExpired" ); }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = oxNew('Category_Seo');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("markRelatedAsExpired", $oExcp->getMessage(), "Error in Category_Seo::Save()");

            return;
        }
        $this->fail("Error in Category_Seo::Save()");
    }

    /**
     * Category_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Category_Seo');
        $this->assertTrue($oView->UNITgetEncoder() instanceof SeoEncoderCategory);
    }

    /**
     * Category_Seo::isSuffixSupported() test case
     *
     * @return null
     */
    public function testIsSuffixSupported()
    {
        $oView = oxNew('Category_Seo');
        $this->assertTrue($oView->isSuffixSupported());
    }

    /**
     * Category_Seo::isEntrySuffixed() test case
     *
     * @return null
     */
    public function testIsEntrySuffixed()
    {
        $sQ1 = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`, oxshowsuffix) " .
               "values ('_test1','test',1,'1','4','test','','','','','1','10','50', '1')";

        $sQ2 = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`, oxshowsuffix) " .
               "values ('_test2','test',1,'1','4','test','','','','','1','10','50', '0')";

        $this->addToDatabase($sQ1, 'oxcategories');
        $this->addToDatabase($sQ2, 'oxcategories');
        $this->addTeardownSql("delete from oxcategories where oxid like '%_test%'");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategorySeo::class, array("getEditObjectId"));
        $oView->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->at(1))->method('getEditObjectId')->will($this->returnValue("_test2"));
        $this->assertTrue($oView->isEntrySuffixed());
        $this->assertFalse($oView->isEntrySuffixed());
    }

    /**
     * Category_Seo::getEntryUri() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $sQ1 = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`, oxshowsuffix) " .
               "values ('_test1','test',1,'1','4','test','','','','','1','10','50', '1')";
        $this->addToDatabase($sQ1, 'oxcategories');
        $this->addTeardownSql("delete from oxcategories where oxid like '%_test%'");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, array("getCategoryUri"));
        $oEncoder->expects($this->once())->method('getCategoryUri')->will($this->returnValue("CategoryUri"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategorySeo::class, array("getEditObjectId", "_getEncoder"));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue("_test1"));
        $oView->expects($this->once())->method('_getEncoder')->will($this->returnValue($oEncoder));
        $this->assertEquals("CategoryUri", $oView->getEntryUri());
    }

    /**
     * Vendor_Seo::_getStdUrl() test case
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $sQ1 = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`, oxshowsuffix) " .
               "values ('_test1','test',1,'1','4','test','','','','','1','10','50', '1')";
        $this->addToDatabase($sQ1, 'oxcategories');
        $this->addTeardownSql("delete from oxcategories where oxid like '%_test%'");

        $oCategory = oxNew('oxCategory');
        $oCategory->load("_test1");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategorySeo::class, array("getEditLang"));
        $oView->expects($this->once())->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals($oCategory->getBaseStdLink(0, true, false), $oView->UNITgetStdUrl("_test1"));
    }
}
